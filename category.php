<?php
session_start();
require_once 'db.php';

// Check login
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$category_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$category_name = 'All News';

// Get Category Name
if ($category_id > 0) {
    $stmt = $pdo->prepare("SELECT name FROM categories WHERE id = ?");
    $stmt->execute([$category_id]);
    $cat = $stmt->fetch();
    if ($cat) {
        $category_name = $cat['name'];
    } else {
        header("Location: home.php"); // Invalid category
        exit;
    }
}

// Fetch Articles for this Category
$stmt = $pdo->prepare("SELECT * FROM articles WHERE category_id = ? ORDER BY created_at DESC");
$stmt->execute([$category_id]);
$articles = $stmt->fetchAll();

// Fetch Categories for Navbar (reuse)
$cats_stmt = $pdo->query("SELECT * FROM categories ORDER BY name");
$categories = $cats_stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CNN Clone - <?php echo htmlspecialchars($category_name); ?></title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700;900&family=Rajdhani:wght@300;500;700&display=swap');

        :root {
            --neon-red: #ff0055;
            --neon-blue: #00f3ff;
            --neon-purple: #bd00ff;
            --bg-dark: #050505;
            --glass-bg: rgba(20, 20, 20, 0.6);
            --glass-border: rgba(255, 255, 255, 0.1);
            --text-main: #ffffff;
            --text-muted: #888888;
        }

        * {
            margin: 0;
            padding: 0;
            box_sizing: border-box;
        }

        body {
            background-color: var(--bg-dark);
            color: var(--text-main);
            font-family: 'Rajdhani', sans-serif;
            overflow-x: hidden;
            background-image:
                linear-gradient(45deg, rgba(0, 0, 0, 1) 0%, rgba(20, 20, 20, 1) 100%);
            min-height: 100vh;
        }

        /* Background animation */
        body::after {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background:
                radial-gradient(circle at 10% 10%, rgba(0, 243, 255, 0.03) 0%, transparent 50%),
                radial-gradient(circle at 90% 90%, rgba(255, 0, 85, 0.03) 0%, transparent 50%);
            z-index: -1;
            pointer-events: none;
        }

        a {
            text-decoration: none;
            color: inherit;
            transition: 0.3s;
        }

        /* HEADER */
        header {
            position: sticky;
            top: 0;
            z-index: 1000;
            background: rgba(5, 5, 5, 0.85);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--glass-border);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.5);
        }

        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 40px;
            max-width: 1600px;
            margin: 0 auto;
        }

        .logo {
            font-family: 'Orbitron', sans-serif;
            font-size: 2.5rem;
            font-weight: 900;
            letter-spacing: -1px;
            background: linear-gradient(90deg, #fff, var(--neon-red));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-shadow: 0 0 20px rgba(255, 0, 85, 0.4);
        }

        .logo span {
            color: var(--neon-blue);
            -webkit-text-fill-color: var(--neon-blue);
        }

        .nav-links {
            display: flex;
            gap: 30px;
        }

        .nav-links a {
            font-size: 1.1rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--text-muted);
            position: relative;
        }

        .nav-links a::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 0%;
            height: 2px;
            background: var(--neon-blue);
            transition: 0.3s;
            box-shadow: 0 0 10px var(--neon-blue);
        }

        .nav-links a:hover,
        .nav-links a.active {
            color: white;
        }

        .nav-links a:hover::after,
        .nav-links a.active::after {
            width: 100%;
        }

        .nav-links a.active {
            color: var(--neon-blue);
            text-shadow: 0 0 10px var(--neon-blue);
        }

        .user-menu {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .logout-btn {
            padding: 8px 20px;
            border: 1px solid var(--neon-red);
            border-radius: 4px;
            color: var(--neon-red);
            font-size: 0.9rem;
            font-weight: bold;
            text-transform: uppercase;
        }

        .logout-btn:hover {
            background: var(--neon-red);
            color: white;
            box-shadow: 0 0 20px var(--neon-red);
        }

        /* CONTENT */
        .container {
            max-width: 1600px;
            margin: 40px auto;
            padding: 0 30px;
        }

        .page-header {
            margin-bottom: 60px;
            text-align: center;
            position: relative;
            padding: 40px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        .page-title {
            font-family: 'Orbitron', sans-serif;
            font-size: 4rem;
            text-transform: uppercase;
            letter-spacing: 5px;
            color: transparent;
            -webkit-text-stroke: 1px rgba(255, 255, 255, 0.8);
            text-shadow: 0 0 30px rgba(0, 243, 255, 0.2);
            position: relative;
            display: inline-block;
        }

        .page-title::before {
            content: attr(data-text);
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            color: var(--neon-blue);
            opacity: 0.5;
            filter: blur(10px);
            z-index: -1;
        }

        .news-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 40px;
        }

        .news-card {
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            border-radius: 12px;
            overflow: hidden;
            transition: 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            backdrop-filter: blur(10px);
            display: flex;
            flex-direction: column;
            group;
        }

        .news-card:hover {
            transform: translateY(-10px);
            border-color: var(--neon-blue);
            box-shadow: 0 0 40px rgba(0, 243, 255, 0.1);
        }

        .news-img-wrap {
            height: 240px;
            position: relative;
            overflow: hidden;
        }

        .news-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.6s;
        }

        .news-card:hover .news-img {
            transform: scale(1.1);
        }

        .news-body {
            padding: 30px;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .news-title {
            font-family: 'Orbitron', sans-serif;
            font-size: 1.5rem;
            margin-bottom: 15px;
            font-weight: 700;
            line-height: 1.3;
            color: #fff;
            transition: 0.3s;
        }

        .news-card:hover .news-title {
            color: var(--neon-blue);
        }

        .news-excerpt {
            font-size: 1rem;
            color: #aaa;
            margin-bottom: 25px;
            line-height: 1.6;
        }

        .news-meta {
            font-size: 0.9rem;
            color: var(--text-muted);
            margin-top: auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
            padding-top: 15px;
        }

        .read-more-btn {
            font-size: 0.8rem;
            font-weight: bold;
            color: var(--neon-red);
            text-transform: uppercase;
            letter-spacing: 1px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .read-more-btn::after {
            content: '→';
            font-size: 1.2rem;
            transition: 0.3s;
        }

        .news-card:hover .read-more-btn::after {
            transform: translateX(5px);
        }

        .no-articles {
            grid-column: 1 / -1;
            text-align: center;
            padding: 80px;
            background: var(--glass-bg);
            border-radius: 20px;
            border: 1px dashed rgba(255, 255, 255, 0.1);
        }

        .no-articles h3 {
            font-family: 'Orbitron';
            font-size: 2rem;
            color: var(--text-muted);
            margin-bottom: 20px;
        }

        @media (max-width: 900px) {
            .navbar {
                flex-direction: column;
                gap: 20px;
            }

            .nav-links {
                flex-wrap: wrap;
                justify-content: center;
            }

            .page-title {
                font-size: 2.5rem;
            }

            .news-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>

    <header>
        <div class="navbar">
            <a href="home.php" class="logo">CNN <span>NEO</span></a>
            <div class="nav-links">
                <a href="home.php">Home</a>
                <?php foreach ($categories as $cat): ?>
                    <a href="category.php?id=<?php echo $cat['id']; ?>"
                        class="<?php echo $cat['id'] == $category_id ? 'active' : ''; ?>">
                        <?php echo htmlspecialchars($cat['name']); ?>
                    </a>
                <?php endforeach; ?>
            </div>
            <div class="user-menu">
                <span
                    style="color:white; font-family:'Orbitron'; font-size:0.9rem;"><?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                <a href="logout.php" class="logout-btn">LOGOUT</a>
            </div>
        </div>
    </header>

    <div class="container">
        <div class="page-header">
            <h1 class="page-title" data-text="<?php echo htmlspecialchars($category_name); ?>">
                <?php echo htmlspecialchars($category_name); ?></h1>
        </div>

        <div class="news-grid">
            <?php if (count($articles) > 0): ?>
                <?php foreach ($articles as $article): ?>
                    <a href="article.php?id=<?php echo $article['id']; ?>" class="news-card">
                        <div class="news-img-wrap">
                            <img src="<?php echo htmlspecialchars($article['image_url']); ?>" class="news-img" alt="News">
                        </div>
                        <div class="news-body">
                            <h3 class="news-title"><?php echo htmlspecialchars($article['title']); ?></h3>
                            <p class="news-excerpt"><?php echo substr(htmlspecialchars($article['content']), 0, 120); ?>...</p>
                            <div class="news-meta">
                                <span><?php echo date('M d, Y', strtotime($article['created_at'])); ?></span>
                                <span class="read-more-btn">Read Article</span>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-articles">
                    <h3>No signals detected.</h3>
                    <p style="color:#666">No Intelligence reports found in this sector.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

</body>

</html>
