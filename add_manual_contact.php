<?php
session_start();
include_once "db.php";
$user_id = $_SESSION['user_id'];
$name = mysqli_real_escape_string($conn, $_POST['name']);
$phone = mysqli_real_escape_string($conn, $_POST['phone']);

if (!empty($name) && !empty($phone)) {
    // Check if user with this phone exists
    $sql = mysqli_query($conn, "SELECT user_id FROM users WHERE phone = '{$phone}'");
    if (mysqli_num_rows($sql) > 0) {
        $row = mysqli_fetch_assoc($sql);
        $saved_user_id = $row['user_id'];

        // Insert into contacts with the specific name given by the user
        $sql2 = mysqli_query($conn, "INSERT INTO contacts (user_id, saved_user_id, saved_name) 
                                     VALUES ({$user_id}, {$saved_user_id}, '{$name}')
                                     ON DUPLICATE KEY UPDATE saved_name = '{$name}'");
        if ($sql2) {
            echo "success";
        } else {
            echo "error";
        }
    } else {
        echo "not_found";
    }
}
?>
