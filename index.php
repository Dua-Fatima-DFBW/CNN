<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("location: home.php");
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WhatsApp Clone | Connect Privately</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <div class="hero fade-in">
        <i class="fab fa-whatsapp" style="font-size: 80px; color: var(--accent); margin-bottom: 20px;"></i>
        <h1>Whisper</h1>
        <p>Simple. Secure. Reliable messaging. Experience the next generation of private communication with our premium
            encrypted chat platform.</p>

        <div style="display: flex; gap: 20px;">
            <a href="login.php" class="auth-btn" style="text-decoration: none; padding: 12px 40px;">Login</a>
            <a href="signup.php" class="auth-btn"
                style="text-decoration: none; padding: 12px 40px; background: transparent; border: 1px solid var(--accent);">Sign
                Up</a>
        </div>

        <div style="margin-top: 50px; display: flex; gap: 40px; color: var(--text-secondary);">
            <div><i class="fas fa-lock"></i> End-to-end encrypted</div>
            <div><i class="fas fa-image"></i> Media sharing</div>
            <div><i class="fas fa-phone"></i> Voice calls</div>
        </div>
    </div>
</body>

</html>
