<?php
session_start();
require_once 'db.php';

// Check login
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id === 0) {
    header("Location: home.php");
    exit;
}

// Fetch Article
$stmt = $pdo->prepare("SELECT a.*, c.name as category_name FROM articles a LEFT JOIN categories c ON a.category_id = c.id WHERE a.id = ?");
$stmt->execute([$id]);
$article = $stmt->fetch();

if (!$article) {
    die("Article not found.");
}

// Fetch Related Articles (Same Category, Exclude Current)
$related_stmt = $pdo->prepare("SELECT * FROM articles WHERE category_id = ? AND id != ? ORDER BY created_at DESC LIMIT 3");
$related_stmt->execute([$article['category_id'], $id]);
$related_articles = $related_stmt->fetchAll();

// Fetch Categories for Navbar
$cats_stmt = $pdo->query("SELECT * FROM categories ORDER BY name");
$categories = $cats_stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CNN Clone -
        <?php echo htmlspecialchars($article['title']); ?>
    </title>
    <style>
        :root {
            --neon-red: #ff003c;
            --neon-blue: #00f3ff;
            --bg-dark: #090909;
            --card-bg: rgba(255, 255, 255, 0.03);
            --header-bg: rgba(0, 0, 0, 0.8);
            --text-main: #ffffff;
            --text-muted: #aaaaaa;
        }

        * {
            margin: 0;
            padding: 0;
            box_sizing: border-box;
            font-family: 'Segoe UI', sans-serif;
        }

        body {
            background-color: var(--bg-dark);
            color: var(--text-main);
            overflow-x: hidden;
            min-height: 100vh;
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
            background: var(--header-bg);
            backdrop-filter: blur(15px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 5%;
            max-width: 1400px;
            margin: 0 auto;
        }

        .logo {
            font-size: 2rem;
            font-weight: 800;
            letter-spacing: -2px;
            color: white;
            text-shadow: 2px 2px 0px var(--neon-red);
        }

        .logo span {
            color: var(--neon-red);
        }

        .nav-links {
            display: flex;
            gap: 20px;
        }

        .nav-links a {
            font-size: 0.9rem;
            font-weight: 600;
            text-transform: uppercase;
            color: var(--text-muted);
        }

        .nav-links a:hover {
            color: var(--neon-blue);
            text-shadow: 0 0 10px var(--neon-blue);
        }

        .user-menu {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-name {
            color: var(--neon-blue);
        }

        .logout-btn {
            padding: 8px 20px;
            border: 1px solid var(--neon-red);
            border-radius: 4px;
            color: var(--neon-red);
            font-size: 0.8rem;
        }

        .logout-btn:hover {
            background: var(--neon-red);
            color: white;
            box-shadow: 0 0 15px var(--neon-red);
        }

        /* ARTICLE CONTENT */
        .container {
            max-width: 1000px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .article-header {
            margin-bottom: 30px;
            text-align: center;
        }

        .category-tag {
            display: inline-block;
            background: transparent;
            border: 1px solid var(--neon-blue);
            color: var(--neon-blue);
            padding: 5px 15px;
            border-radius: 20px;
            text-transform: uppercase;
            font-size: 0.8rem;
            margin-bottom: 20px;
            font-weight: bold;
        }

        .article-title {
            font-size: 3rem;
            line-height: 1.1;
            margin-bottom: 20px;
            font-weight: 800;
        }

        .article-meta {
            color: var(--text-muted);
            font-size: 1rem;
            margin-bottom: 30px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding-bottom: 20px;
        }

        .article-image {
            width: 100%;
            height: 500px;
            object-fit: cover;
            border-radius: 12px;
            margin-bottom: 40px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.5);
        }

        .article-body {
            font-size: 1.2rem;
            line-height: 1.8;
            color: #ddd;
            margin-bottom: 60px;
        }

        .article-body p {
            margin-bottom: 20px;
        }

        /* RELATED SECTION */
        .related-section {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding-top: 40px;
        }

        .related-title {
            font-size: 1.5rem;
            margin-bottom: 20px;
            color: var(--neon-red);
            text-transform: uppercase;
        }

        .related-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }

        .related-card {
            background: var(--card-bg);
            border-radius: 8px;
            overflow: hidden;
            display: block;
        }

        .related-card:hover {
            transform: translateY(-5px);
        }

        .related-img {
            width: 100%;
            height: 150px;
            object-fit: cover;
        }

        .related-info {
            padding: 15px;
        }

        .related-headline {
            font-size: 1rem;
            font-weight: bold;
            margin-bottom: 5px;
        }

        @media (max-width: 800px) {
            .article-title {
                font-size: 2rem;
            }

            .article-image {
                height: 300px;
            }

            .related-grid {
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
                    <a href="category.php?id=<?php echo $cat['id']; ?>">
                        <?php echo htmlspecialchars($cat['name']); ?>
                    </a>
                <?php endforeach; ?>
            </div>
            <div class="user-menu">
                <span class="user-name">
                    <?php echo htmlspecialchars($_SESSION['user_name']); ?>
                </span>
                <a href="logout.php" class="logout-btn">LOGOUT</a>
            </div>
        </div>
    </header>

    <div class="container">
        <div class="article-header">
            <span class="category-tag">
                <?php echo htmlspecialchars($article['category_name']); ?>
            </span>
            <h1 class="article-title">
                <?php echo htmlspecialchars($article['title']); ?>
            </h1>
            <div class="article-meta">
                By <strong>
                    <?php echo htmlspecialchars($article['author']); ?>
                </strong> |
                Updated
                <?php echo date('F d, Y, h:i A', strtotime($article['created_at'])); ?>
            </div>
        </div>

        <img src="<?php echo htmlspecialchars($article['image_url']); ?>" alt="Article Image" class="article-image">

        <div class="article-body">
            <?php echo nl2br(htmlspecialchars($article['content'])); ?>
        </div>

        <div class="related-section">
            <h3 class="related-title">More in
                <?php echo htmlspecialchars($article['category_name']); ?>
            </h3>
            <div class="related-grid">
                <?php foreach ($related_articles as $related): ?>
                    <a href="article.php?id=<?php echo $related['id']; ?>" class="related-card">
                        <img src="<?php echo htmlspecialchars($related['image_url']); ?>" class="related-img" alt="Related">
                        <div class="related-info">
                            <h4 class="related-headline">
                                <?php echo htmlspecialchars($related['title']); ?>
                            </h4>
                        </div>
                    </a>
                <?php endforeach; ?>
                <?php if (empty($related_articles))
                    echo '<p style="color:#666">No other articles in this category.</p>'; ?>
            </div>
        </div>
    </div>

</body>

</html>
