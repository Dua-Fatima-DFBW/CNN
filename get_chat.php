<?php
session_start();
if (isset($_SESSION['user_id'])) {
    include_once "db.php";
    $outgoing_id = $_SESSION['user_id'];
    $incoming_id = mysqli_real_escape_string($conn, $_POST['incoming_id']);
    $output = "";

    $sql = "SELECT * FROM messages 
            WHERE (outgoing_msg_id = {$outgoing_id} AND incoming_msg_id = {$incoming_id})
            OR (outgoing_msg_id = {$incoming_id} AND incoming_msg_id = {$outgoing_id}) ORDER BY msg_id";
    $query = mysqli_query($conn, $sql);

    if (mysqli_num_rows($query) > 0) {
        while ($row = mysqli_fetch_assoc($query)) {
            $time = date('H:i', strtotime($row['created_at']));
            if ($row['outgoing_msg_id'] === $outgoing_id) {
                $output .= '<div class="message outgoing fade-in">';
                if ($row['msg_type'] == 'image') {
                    $output .= '<img src="' . $row['file_path'] . '">';
                }
                if ($row['msg']) {
                    $output .= '<p>' . htmlspecialchars($row['msg']) . '</p>';
                }
                $output .= '<div class="message-time">' . $time . '</div></div>';
            } else {
                $output .= '<div class="message incoming fade-in">';
                if ($row['msg_type'] == 'image') {
                    $output .= '<img src="' . $row['file_path'] . '">';
                }
                if ($row['msg']) {
                    $output .= '<p>' . htmlspecialchars($row['msg']) . '</p>';
                }
                $output .= '<div class="message-time">' . $time . '</div></div>';
            }
        }
    } else {
        $output .= '<div style="text-align: center; margin-top: 50px; color: var(--text-secondary);">No messages are available. Once you send message they will appear here.</div>';
    }
    echo $output;
} else {
    header("location: login.php");
}
?>
