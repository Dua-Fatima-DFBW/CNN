<?php
session_start();
include_once "db.php";
$user_id = $_SESSION['user_id'];
$saved_user_id = mysqli_real_escape_string($conn, $_POST['user_id']);
 
if (!empty($saved_user_id)) {
    $sql = mysqli_query($conn, "INSERT IGNORE INTO contacts (user_id, saved_user_id) VALUES ({$user_id}, {$saved_user_id})");
    if ($sql) {
        echo "success";
    } else {
        echo "error";
    }
}
?>
