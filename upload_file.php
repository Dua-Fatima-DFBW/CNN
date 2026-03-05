<?php
session_start();
if (isset($_SESSION['user_id'])) {
    include_once "db.php";
    $outgoing_id = $_SESSION['user_id'];
    $incoming_id = mysqli_real_escape_string($conn, $_POST['incoming_id']);

    if (isset($_FILES['image'])) {
        $img_name = $_FILES['image']['name'];
        $img_type = $_FILES['image']['type'];
        $tmp_name = $_FILES['image']['tmp_name'];

        $img_explode = explode('.', $img_name);
        $img_ext = end($img_explode);

        $extensions = ["png", "jpeg", "jpg"];
        if (in_array($img_ext, $extensions) === true) {
            $time = time();
            $new_img_name = $time . $img_name;
            if (move_uploaded_file($tmp_name, "uploads/" . $new_img_name)) {
                $file_path = "uploads/" . $new_img_name;
                $sql = mysqli_query($conn, "INSERT INTO messages (incoming_msg_id, outgoing_msg_id, msg, msg_type, file_path)
                                    VALUES ({$incoming_id}, {$outgoing_id}, '', 'image', '{$file_path}')") or die();
                echo "success";
            }
        }
    }
}
?>
