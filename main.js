const contactList = document.querySelector("#contact-list");
const chatWindow = document.querySelector("#chat-window");
const noChatScreen = document.querySelector("#no-chat-screen");
const messagesArea = document.querySelector("#messages-area");
const messageInput = document.querySelector("#message-input");
const sendBtn = document.querySelector("#send-btn");
const searchInput = document.querySelector("#contact-search");

let activeChatId = null;
let chatTimer = null;

// Load Contacts initially and search
setInterval(() => {
    if (!searchInput.value) { // Don't refresh if searching
        fetchContacts();
    }
}, 3000);

function fetchContacts() {
    let xhr = new XMLHttpRequest();
    xhr.open("GET", "get_contacts.php", true);
    xhr.onload = () => {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                contactList.innerHTML = xhr.response;
            }
        }
    }
    xhr.send();
}

// Search Users
searchInput.onkeyup = () => {
    let searchTerm = searchInput.value;
    if (searchTerm != "") {
        let xhr = new XMLHttpRequest();
        xhr.open("POST", "search_contacts_active.php", true); // We'll create this or reuse search_users logic
        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhr.onload = () => {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    contactList.innerHTML = xhr.response;
                }
            }
        }
        xhr.send("searchTerm=" + searchTerm);
    } else {
        fetchContacts();
    }
}

function openChat(userId, name, img, status) {
    activeChatId = userId;
    noChatScreen.style.display = "none";
    chatWindow.style.display = "flex";

    document.querySelector("#active-chat-name").innerText = name;
    document.querySelector("#active-chat-img").src = img;
    document.querySelector("#active-chat-status").innerText = status;

    // Clear old timer and start new one
    if (chatTimer) clearInterval(chatTimer);
    fetchMessages();
    chatTimer = setInterval(fetchMessages, 1000);
}

function fetchMessages() {
    if (!activeChatId) return;
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "get_chat.php", true);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.onload = () => {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                messagesArea.innerHTML = xhr.response;
                scrollToBottom();
            }
        }
    }
    xhr.send("incoming_id=" + activeChatId);
}

sendBtn.onclick = () => {
    sendMessage();
}

messageInput.onkeyup = (e) => {
    if (e.key === "Enter") sendMessage();
}

function sendMessage() {
    let message = messageInput.value;
    if (message == "" || !activeChatId) return;

    let xhr = new XMLHttpRequest();
    xhr.open("POST", "insert_chat.php", true);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.onload = () => {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                messageInput.value = "";
                fetchMessages();
            }
        }
    }
    xhr.send("incoming_id=" + activeChatId + "&message=" + message);
}

function scrollToBottom() {
    messagesArea.scrollTop = messagesArea.scrollHeight;
}

// Modal logic
function openAddContactModal() {
    document.querySelector("#add-contact-modal").style.display = "flex";
}

function openProfileModal() {
    document.querySelector("#profile-modal").style.display = "flex";
}

function closeModal(id) {
    document.getElementById(id).style.display = "none";
}

// Search users to add
document.querySelector("#search-user-input").onkeyup = (e) => {
    let searchTerm = e.target.value;
    if (searchTerm.length > 2) {
        let xhr = new XMLHttpRequest();
        xhr.open("POST", "search_users.php", true);
        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhr.onload = () => {
            if (xhr.status === 200) {
                document.querySelector("#search-results").innerHTML = xhr.response;
            }
        }
        xhr.send("searchTerm=" + searchTerm);
    }
}

function addContact(userId) {
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "add_contact.php", true);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.onload = () => {
        if (xhr.response.trim() === "success") {
            closeModal('add-contact-modal');
            fetchContacts();
        }
    }
    xhr.send("user_id=" + userId);
}

function addManualContact() {
    let name = document.querySelector("#manual-name").value;
    let phone = document.querySelector("#manual-phone").value;
    if (name == "" || phone == "") return;

    let xhr = new XMLHttpRequest();
    xhr.open("POST", "add_manual_contact.php", true);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.onload = () => {
        if (xhr.response.trim() === "success") {
            closeModal('add-contact-modal');
            fetchContacts();
        } else if (xhr.response.trim() === "not_found") {
            alert("User with this phone number is not on Whisper!");
        }
    }
    xhr.send("name=" + name + "&phone=" + phone);
}

// Upload image in chat
function uploadImage() {
    let fileInput = document.getElementById('file-input');
    if (fileInput.files.length == 0) return;

    let formData = new FormData();
    formData.append("image", fileInput.files[0]);
    formData.append("incoming_id", activeChatId);

    let xhr = new XMLHttpRequest();
    xhr.open("POST", "upload_file.php", true);
    xhr.onload = () => {
        if (xhr.response.trim() === "success") {
            fetchMessages();
            fileInput.value = "";
        }
    }
    xhr.send(formData);
}

// Profile updates
function updateProfileImg(input) {
    if (input.files && input.files[0]) {
        let reader = new FileReader();
        reader.onload = function (e) {
            document.querySelector("#profile-preview").src = e.target.result;
        }
        reader.readAsDataURL(input.files[0]);
    }
}

function saveProfile() {
    let name = document.querySelector("#profile-name").value;
    let about = document.querySelector("#profile-about").value;
    let fileInput = document.querySelector("#profile-upload");

    let formData = new FormData();
    formData.append("name", name);
    formData.append("about", about);
    if (fileInput.files.length > 0) {
        formData.append("image", fileInput.files[0]);
    }

    let xhr = new XMLHttpRequest();
    xhr.open("POST", "update_profile.php", true);
    xhr.onload = () => {
        if (xhr.response.trim() === "success") {
            location.reload();
        }
    }
    xhr.send(formData);
}

// Call Simulation
function startCall(type) {
    const overlay = document.getElementById('call-overlay');
    const name = document.getElementById('active-chat-name').innerText;
    const img = document.getElementById('active-chat-img').src;

    document.getElementById('call-name').innerText = name;
    document.getElementById('call-avatar').src = img;
    document.getElementById('call-status').innerText = (type === 'video' ? 'Outgoing video call...' : 'Outgoing voice call...');

    overlay.style.display = 'flex';

    // Simulate answering after 3 seconds
    setTimeout(() => {
        document.getElementById('call-status').innerText = 'Connected';
    }, 3000);
}

function endCall() {
    document.getElementById('call-overlay').style.display = 'none';
}

function toggleEmojiPicker() {
    // Simple alert for now, but in reality we'd pull up an emoji list
    // Or just append a random emoji for demo
    const emojis = ['😊', '😂', '🔥', '👍', '❤️', '🙌', '✨', '✔'];
    const randomEmoji = emojis[Math.floor(Math.random() * emojis.length)];
    messageInput.value += randomEmoji;
}

// Initial fetch
fetchContacts();
