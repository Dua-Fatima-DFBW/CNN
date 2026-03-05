<?php
session_start();
include_once "db.php";

if (isset($_SESSION['user_id'])) {
    header("location: home.php");
}

$error = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email_phone = mysqli_real_escape_string($conn, $_POST['email_phone']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    if (!empty($email_phone) && !empty($password)) {
        $sql = mysqli_query($conn, "SELECT * FROM users WHERE email = '{$email_phone}' OR phone = '{$email_phone}'");
        if (mysqli_num_rows($sql) > 0) {
            $row = mysqli_fetch_assoc($sql);
            if (password_verify($password, $row['password'])) {
                $_SESSION['user_id'] = $row['user_id'];
                header("location: home.php");
            } else {
                $error = "Incorrect password!";
            }
        } else {
            $error = "Email or Phone not found!";
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
    <title>Login | Whisper</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="auth-container">
        <div class="auth-card fade-in">
            <h2 style="display: flex; align-items: center; justify-content: center; gap: 10px;">
                <i class="fab fa-whatsapp" style="color: var(--accent);"></i> Whisper
            </h2>
            <?php if ($error): ?>
                <div
                    style="background: rgba(234, 0, 56, 0.1); color: #ea0038; padding: 10px; border-radius: 5px; margin-bottom: 20px; text-align: center; font-size: 14px;">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            <form action="login.php" method="POST">
                <div class="form-group">
                    <label>Email or Phone</label>
                    <input type="text" name="email_phone" placeholder="Enter email or phone" required>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" placeholder="Enter your password" required>
                </div>
                <button type="submit" class="auth-btn">Login</button>
            </form>
            <div class="auth-link">
                Don't have an account? <a href="signup.php">Sign up now</a>
            </div>
        </div>
    </div>
</body>

</html>
