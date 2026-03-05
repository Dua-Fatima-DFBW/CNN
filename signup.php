<?php
session_start();
include_once "db.php";

if (isset($_SESSION['user_id'])) {
    header("location: home.php");
}

$error = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    if (!empty($username) && !empty($email) && !empty($phone) && !empty($password)) {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $sql = mysqli_query($conn, "SELECT email FROM users WHERE email = '{$email}'");
            if (mysqli_num_rows($sql) > 0) {
                $error = "$email - This email already exists!";
            } else {
                $sql2 = mysqli_query($conn, "SELECT phone FROM users WHERE phone = '{$phone}'");
                if (mysqli_num_rows($sql2) > 0) {
                    $error = "$phone - This number already exists!";
                } else {
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    $insert_query = mysqli_query($conn, "INSERT INTO users (username, email, phone, password, profile_image) 
                                     VALUES ('{$username}', '{$email}', '{$phone}', '{$hashed_password}', 'default.png')");
                    if ($insert_query) {
                        $select_sql2 = mysqli_query($conn, "SELECT * FROM users WHERE email = '{$email}'");
                        if (mysqli_num_rows($select_sql2) > 0) {
                            $result = mysqli_fetch_assoc($select_sql2);
                            $_SESSION['user_id'] = $result['user_id'];
                            header("location: home.php");
                        }
                    } else {
                        $error = "Something went wrong. Please try again!";
                    }
                }
            }
        } else {
            $error = "$email is not a valid email!";
        }
    } else {
        $error = "All input fields are required!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up | Whisper</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="auth-container">
        <div class="auth-card fade-in">
            <h2>Create Account</h2>
            <?php if ($error): ?>
                <div
                    style="background: rgba(234, 0, 56, 0.1); color: #ea0038; padding: 10px; border-radius: 5px; margin-bottom: 20px; text-align: center; font-size: 14px;">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            <form action="signup.php" method="POST">
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="username" placeholder="Enter your name" required>
                </div>
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" placeholder="Enter your email" required>
                </div>
                <div class="form-group">
                    <label>Phone Number</label>
                    <input type="text" name="phone" placeholder="Enter your phone" required>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" placeholder="Create a password" required>
                </div>
                <button type="submit" class="auth-btn">Sign Up</button>
            </form>
            <div class="auth-link">
                Already have an account? <a href="login.php">Login now</a>
            </div>
        </div>
    </div>
</body>

</html>
