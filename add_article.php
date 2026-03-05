<?php
session_start();
require_once 'db.php';

// Check login
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$success = '';
$error = '';

// Fetch Categories
$cats_stmt = $pdo->query("SELECT * FROM categories ORDER BY name");
$categories = $cats_stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $category_id = intval($_POST['category_id']);
    $image_url = trim($_POST['image_url']);
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $is_breaking = isset($_POST['is_breaking']) ? 1 : 0;
    $author = $_SESSION['user_name']; // Use logged in user as author

    if (empty($title) || empty($content) || empty($category_id)) {
        $error = "Please fill in all required fields.";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO articles (title, content, category_id, image_url, author, is_featured, is_breaking) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$title, $content, $category_id, $image_url, $author, $is_featured, $is_breaking]);
            $success = "Article published successfully!";
        } catch (PDOException $e) {
            $error = "Error: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CNN Clone - Add Article</title>
    <style>
        :root {
            --neon-red: #ff003c;
            --neon-blue: #00f3ff;
            --bg-dark: #090909;
            --card-bg: rgba(255, 255, 255, 0.05);
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
            min-height: 100vh;
        }

        a {
            text-decoration: none;
            color: inherit;
            transition: 0.3s;
        }

        .container {
            max-width: 800px;
            margin: 50px auto;
            padding: 0 20px;
        }

        .form-card {
            background: var(--card-bg);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 40px;
            box-shadow: 0 0 30px rgba(0, 0, 0, 0.3);
        }

        h1 {
            text-align: center;
            margin-bottom: 30px;
            color: var(--neon-blue);
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: var(--text-muted);
            font-weight: bold;
        }

        input[type="text"],
        textarea,
        select {
            width: 100%;
            padding: 12px;
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 6px;
            color: white;
            font-size: 1rem;
        }

        input:focus,
        textarea:focus,
        select:focus {
            border-color: var(--neon-blue);
            outline: none;
            box-shadow: 0 0 10px rgba(0, 243, 255, 0.1);
        }

        textarea {
            height: 200px;
            resize: vertical;
        }

        .checkbox-group {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }

        .checkbox-item {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
        }

        input[type="checkbox"] {
            width: 20px;
            height: 20px;
            accent-color: var(--neon-red);
        }

        button {
            width: 100%;
            padding: 15px;
            background: linear-gradient(45deg, var(--neon-red), darkred);
            border: none;
            border-radius: 8px;
            color: white;
            font-size: 1.1rem;
            font-weight: bold;
            cursor: pointer;
            text-transform: uppercase;
            transition: 0.3s;
        }

        button:hover {
            box-shadow: 0 0 20px rgba(255, 0, 60, 0.4);
            transform: translateY(-2px);
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: var(--text-muted);
        }

        .back-link:hover {
            color: white;
        }

        .alert {
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            text-align: center;
        }

        .alert-success {
            background: rgba(0, 255, 128, 0.1);
            border: 1px solid #00ff80;
            color: #00ff80;
        }

        .alert-error {
            background: rgba(255, 0, 60, 0.1);
            border: 1px solid #ff003c;
            color: #ff003c;
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="form-card">
            <h1>Publish Article</h1>

            <?php if ($success): ?>
                <div class="alert alert-success">
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="alert alert-error">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label>Headline</label>
                    <input type="text" name="title" required placeholder="Enter article headline">
                </div>

                <div class="form-group">
                    <label>Category</label>
                    <select name="category_id" required>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo $cat['id']; ?>">
                                <?php echo htmlspecialchars($cat['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Cover Image URL</label>
                    <input type="text" name="image_url" placeholder="https://...">
                </div>

                <div class="form-group">
                    <label>Content</label>
                    <textarea name="content" required placeholder="Write your story..."></textarea>
                </div>

                <div class="checkbox-group">
                    <label class="checkbox-item">
                        <input type="checkbox" name="is_featured"> Featured
                    </label>
                    <label class="checkbox-item">
                        <input type="checkbox" name="is_breaking"> Breaking News
                    </label>
                </div>

                <button type="submit">Publish Article</button>
            </form>

            <a href="home.php" class="back-link">Back to Dashboard</a>
        </div>
    </div>

</body>

</html>
