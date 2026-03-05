<?php
session_start();
include_once "db.php";
if (!isset($_SESSION['user_id'])) {
    header("location: login.php");
}
$user_id = $_SESSION['user_id'];
$sql = mysqli_query($conn, "SELECT * FROM users WHERE user_id = {$user_id}");
if (mysqli_num_rows($sql) > 0) {
    $row = mysqli_fetch_assoc($sql);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Whisper | Chat</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <div class="app-wrapper">
        <!-- Sidebar -->
        <aside class="sidebar">
            <header class="sidebar-header">
                <img src="uploads/<?php echo $row['profile_image']; ?>" alt="Profile" class="profile-img"
                    onclick="openProfileModal()">
                <div class="header-icons">
                    <i class="fas fa-circle-notch"></i>
                    <i class="fas fa-comment-alt" onclick="openAddContactModal()"></i>
                    <i class="fas fa-ellipsis-v"></i>
                </div>
            </header>

            <div class="search-container">
                <div class="search-box">
                    <i class="fas fa-search" style="color: var(--text-secondary);"></i>
                    <input type="text" id="contact-search" placeholder="Search or start new chat">
                </div>
            </div>

            <div class="contact-list" id="contact-list">
                <!-- Contacts will be loaded here via JS -->
                <div style="padding: 20px; text-align: center; color: var(--text-secondary);">
                    Loading contacts...
                </div>
            </div>
        </aside>

        <!-- Main Chat Area -->
        <main class="chat-container">
            <!-- No chat selected state -->
            <div id="no-chat-screen" class="no-chat-selected">
                <i class="fab fa-whatsapp"></i>
                <h1>Whisper Web</h1>
                <p>Send and receive messages without keeping your phone online.<br>Use Whisper on up to 4 linked devices
                    and 1 phone at the same time.</p>
                <div style="margin-top: 50px; font-size: 13px; color: var(--text-secondary);">
                    <i class="fas fa-lock"></i> End-to-end encrypted
                </div>
            </div>

            <!-- Chat Window (Hidden by default) -->
            <div id="chat-window" style="display: none; flex-direction: column; height: 100%;">
                <header class="chat-header">
                    <div class="chat-user-info">
                        <img src="uploads/default.png" alt="User" class="profile-img" id="active-chat-img">
                        <div class="chat-user-details">
                            <div class="chat-user-name" id="active-chat-name">User Name</div>
                            <div class="chat-status" id="active-chat-status">online</div>
                        </div>
                    </div>
                    <div class="header-icons">
                        <i class="fas fa-video" onclick="startCall('video')"></i>
                        <i class="fas fa-phone-alt" onclick="startCall('voice')"></i>
                        <i class="fas fa-search"></i>
                        <i class="fas fa-ellipsis-v"></i>
                    </div>
                </header>

                <div class="messages-area" id="messages-area">
                    <!-- Messages will be loaded here -->
                </div>

                <div class="chat-input-area">
                    <i class="far fa-smile icon-btn" onclick="toggleEmojiPicker()"></i>
                    <i class="fas fa-paperclip icon-btn" onclick="document.getElementById('file-input').click()"></i>
                    <input type="file" id="file-input" style="display: none;" onchange="uploadImage()">

                    <div class="chat-input-container">
                        <input type="text" id="message-input" placeholder="Type a message" autocomplete="off">
                    </div>

                    <div id="send-btn" class="icon-btn send-btn">
                        <i class="fas fa-paper-plane"></i>
                    </div>
                </div>
            </div>

            <!-- Call Overlay -->
            <div class="call-overlay" id="call-overlay">
                <img src="uploads/default.png" alt="Avatar" class="call-avatar" id="call-avatar">
                <h2 id="call-name">User Name</h2>
                <p id="call-status">Calling...</p>
                <div class="call-btns">
                    <div class="call-btn accept-call" id="accept-btn"><i class="fas fa-phone"></i></div>
                    <div class="call-btn end-call" onclick="endCall()"><i class="fas fa-phone-slash"></i></div>
                </div>
            </div>
        </main>
    </div>

    <!-- Modals -->
    <!-- Add Contact Modal -->
    <div class="modal" id="add-contact-modal">
        <div class="modal-content">
            <div style="display: flex; justify-content: space-between; margin-bottom: 20px;">
                <h3>Add New Contact</h3>
                <i class="fas fa-times" style="cursor: pointer;" onclick="closeModal('add-contact-modal')"></i>
            </div>
            <div class="form-group">
                <label>Search by Email or Phone</label>
                <input type="text" id="search-user-input" placeholder="Enter email or phone number">
            </div>
            <div id="search-results" style="margin-top: 15px;">
                <!-- Search results will appear here -->
            </div>
            <div style="margin-top: 20px;">
                <p style="font-size: 13px; color: var(--text-secondary); margin-bottom: 10px;">Or add manually:</p>
                <div class="form-group">
                    <input type="text" id="manual-name" placeholder="Name">
                </div>
                <div class="form-group">
                    <input type="text" id="manual-phone" placeholder="Phone Number">
                </div>
                <button class="auth-btn" onclick="addManualContact()">Add Contact</button>
            </div>
        </div>
    </div>

    <!-- Profile Modal -->
    <div class="modal" id="profile-modal">
        <div class="modal-content">
            <div style="display: flex; justify-content: space-between; margin-bottom: 20px;">
                <h3>Your Profile</h3>
                <i class="fas fa-times" style="cursor: pointer;" onclick="closeModal('profile-modal')"></i>
            </div>
            <div class="profile-edit">
                <img src="uploads/<?php echo $row['profile_image']; ?>" alt="Profile" class="profile-img-large"
                    id="profile-preview">
                <div class="file-input-wrapper">
                    <span class="file-input-btn">Change Photo</span>
                    <input type="file" id="profile-upload" onchange="updateProfileImg(this)">
                </div>
                <div class="form-group" style="width: 100%;">
                    <label>Your Name</label>
                    <input type="text" id="profile-name" value="<?php echo $row['username']; ?>">
                </div>
                <div class="form-group" style="width: 100%;">
                    <label>About</label>
                    <input type="text" id="profile-about" value="<?php echo $row['status']; ?>">
                </div>
                <button class="auth-btn" onclick="saveProfile()">Save Changes</button>
            </div>
        </div>
    </div>

    <script src="main.js"></script>
</body>

</html>
