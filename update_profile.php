<?php
session_start();
if (isset($_SESSION['user_id'])) {
    include_once "db.php";
    $user_id = $_SESSION['user_id'];
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $about = mysqli_real_escape_string($conn, $_POST['about']);

    // Update name and about
    $sql = mysqli_query($conn, "UPDATE users SET username = '{$name}', status = '{$about}' WHERE user_id = {$user_id}");

    // Update profile image if exists
    if (isset($_FILES['image'])) {
        $img_name = $_FILES['image']['name'];
        $tmp_name = $_FILES['image']['tmp_name'];
        $img_explode = explode('.', $img_name);
        $img_ext = end($img_explode);
        $extensions = ["png", "jpeg", "jpg"];
        if (in_array($img_ext, $extensions) === true) {
            $new_img_name = time() . $img_name;
            if (move_uploaded_file($tmp_name, "uploads/" . $new_img_name)) {
                $sql2 = mysqli_query($conn, "UPDATE users SET profile_image = '{$new_img_name}' WHERE user_id = {$user_id}");
            }
        }
    }
    echo "success";
}
?>
