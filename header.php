<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Include db.php if it hasn't been included yet, to ensure $conn is available for config or dynamic menus if needed.
require_once 'db.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CNN - Breaking News, Latest News and Videos</title>
    <!-- Modern Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Oswald:wght@500;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>

<body>

    <header>
        <nav class="navbar">
            <a href="index.php" class="logo">CNN</a>

            <div class="nav-links">
                <a href="index.php">Home</a>
                <!-- Hardcoded IDs based on the database.sql insert to ensure they match -->
                <a href="category.php?id=2">World</a>
                <a href="category.php?id=1">Politics</a>
                <a href="category.php?id=3">Business</a>
                <a href="category.php?id=4">Tech</a>
                <a href="category.php?id=5">Sports</a>
                <a href="category.php?id=6">Entertainment</a>
            </div>

            <div class="auth-buttons">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <span style="color:white; margin-right:10px;">Hello,
                        <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                    <a href="add_article.php" class="btn-login" style="border: 1px solid var(--neon-blue);">+ Add News</a>
                    <a href="logout.php" class="btn-signup" style="background: #333;">Logout</a>
                <?php else: ?>
                    <a href="login.php" class="btn-login">Log In</a>
                    <a href="signup.php" class="btn-signup">Sign Up</a>
                <?php endif; ?>
            </div>
        </nav>

        <div class="breaking-news">
            <div class="breaking-label">Breaking News</div>
            <div class="ticker-wrap">
                <div class="ticker-move">
                    <div class="ticker-item">Global Summit Reaches Historic Agreement on Climate Action</div>
                    <div class="ticker-item">Tech Giants Report Record Quarterly Earnings Amid Market Rally</div>
                    <div class="ticker-item">New Space Mission Successfully Launches to Mars</div>
                    <div class="ticker-item">Championship Finals: Underdog Team Takes Lead in Overtime</div>
                </div>
            </div>
        </div>
    </header>
