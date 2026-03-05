<?php
session_start();
include_once "db.php";
$outgoing_id = $_SESSION['user_id'];
$searchTerm = mysqli_real_escape_string($conn, $_POST['searchTerm']);

$sql = "SELECT * FROM users WHERE (email LIKE '%{$searchTerm}%' OR phone LIKE '%{$searchTerm}%') AND user_id != {$outgoing_id}";
$query = mysqli_query($conn, $sql);
$output = "";

if (mysqli_num_rows($query) > 0) {
    while ($row = mysqli_fetch_assoc($query)) {
        $output .= '<div style="display: flex; align-items: center; justify-content: space-between; padding: 10px; background: var(--search-bg); border-radius: 8px; margin-bottom: 10px;">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <img src="uploads/' . $row['profile_image'] . '" style="width: 35px; height: 35px; border-radius: 50%;">
                            <div>
                                <div style="font-weight: 500;">' . $row['username'] . '</div>
                                <div style="font-size: 12px; color: var(--text-secondary);">' . $row['phone'] . '</div>
                            </div>
                        </div>
                        <button class="auth-btn" style="width: auto; padding: 5px 15px; font-size: 13px;" onclick="addContact(' . $row['user_id'] . ')">Add</button>
                    </div>';
    }
} else {
    $output .= '<div style="text-align: center; color: var(--text-secondary);">No user found!</div>';
}
echo $output;
?>
