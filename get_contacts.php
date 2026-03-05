<?php
session_start();
include_once "db.php";
$outgoing_id = $_SESSION['user_id'];

// Get unique user IDs of people this user has messaged or has been messaged by
$sql = "SELECT DISTINCT users.user_id, users.username, users.profile_image, users.status, contacts.saved_name 
        FROM users 
        LEFT JOIN messages ON (messages.incoming_msg_id = users.user_id AND messages.outgoing_msg_id = {$outgoing_id}) 
        OR (messages.outgoing_msg_id = users.user_id AND messages.incoming_msg_id = {$outgoing_id})
        LEFT JOIN contacts ON (contacts.saved_user_id = users.user_id AND contacts.user_id = {$outgoing_id})
        WHERE (messages.msg_id IS NOT NULL OR contacts.contact_id IS NOT NULL) 
        AND users.user_id != {$outgoing_id}";

$query = mysqli_query($conn, $sql);
$output = "";

if (mysqli_num_rows($query) == 0) {
    $output .= '<div style="padding: 20px; text-align: center; color: var(--text-secondary);">No contacts found. Start a new chat!</div>';
} elseif (mysqli_num_rows($query) > 0) {
    while ($row = mysqli_fetch_assoc($query)) {
        // Get last message
        $sql2 = "SELECT * FROM messages WHERE (incoming_msg_id = {$row['user_id']} OR outgoing_msg_id = {$row['user_id']}) 
                 AND (outgoing_msg_id = {$outgoing_id} OR incoming_msg_id = {$outgoing_id}) 
                 ORDER BY msg_id DESC LIMIT 1";
        $query2 = mysqli_query($conn, $sql2);
        $row2 = mysqli_fetch_assoc($query2);

        $last_msg = (mysqli_num_rows($query2) > 0) ? $row2['msg'] : "No messages yet";
        if (strlen($last_msg) > 28)
            $last_msg = substr($last_msg, 0, 28) . '...';

        // Handle images in last message
        if (isset($row2['msg_type']) && $row2['msg_type'] == 'image')
            $last_msg = "📷 Photo";

        $time = (mysqli_num_rows($query2) > 0) ? date('H:i', strtotime($row2['created_at'])) : "";

        $contact_name = (!empty($row['saved_name'])) ? $row['saved_name'] : $row['username'];

        $output .= '<div class="contact-item" onclick="openChat(' . $row['user_id'] . ', \'' . addslashes($contact_name) . '\', \'uploads/' . $row['profile_image'] . '\', \'' . $row['status'] . '\')">
                        <img src="uploads/' . $row['profile_image'] . '" alt="" class="profile-img">
                        <div class="contact-info">
                            <div class="contact-name-row">
                                <span class="contact-name">' . $contact_name . '</span>
                                <span class="contact-time">' . $time . '</span>
                            </div>
                            <div class="last-msg">' . $last_msg . '</div>
                        </div>
                    </div>';
    }
}
echo $output;
?>
