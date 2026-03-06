<?php
require_once 'db.php';
require_login();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $image_url = trim($_POST['image_url']);
    $user_id = $_SESSION['user_id'];

    if (empty($title) || empty($content)) {
        $error = "Title and content are required.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO posts (user_id, title, content, image_url) VALUES (?, ?, ?, ?)");
        if ($stmt->execute([$user_id, $title, $content, $image_url])) {
            $_SESSION['msg'] = "Post published successfully!";
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Failed to create post.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Post - BloggerClone</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
</head>
<body>

<header>
    <a href="index.php" class="logo">Blogger.</a>
    <nav class="nav-links">
        <a href="index.php">Home</a>
        <a href="dashboard.php">My Dashboard</a>
        <a href="create_post.php" style="color: var(--primary-color);">New Post</a>
    </nav>
    <div class="auth-btns">
        <span style="margin-right: 15px;">Hi, <strong><?php echo h($_SESSION['username']); ?></strong></span>
        <a href="logout.php" class="btn btn-outline btn-sm">Logout</a>
    </div>
</header>

<div class="container" style="max-width: 900px; margin-top: 3rem;">
    <div class="dashboard-header">
        <h1>Write Dynamic Story</h1>
        <a href="dashboard.php" class="btn btn-outline btn-sm">Back to Dashboard</a>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>

    <div class="form-container" style="max-width: 100%; margin: 0;">
        <form method="POST" action="create_post.php">
            <div class="form-group">
                <label class="form-label">Post Title</label>
                <input type="text" name="title" class="form-control" placeholder="Enter a catchy title..." required>
            </div>
            <div class="form-group">
                <label class="form-label">Featured Image URL (Optional)</label>
                <input type="url" name="image_url" class="form-control" placeholder="https://example.com/image.jpg">
                <small style="color: #888; font-size: 0.8rem;">Provide a public image URL for your post banner.</small>
            </div>
            <div class="form-group">
                <label class="form-label">Story Content</label>
                <textarea name="content" class="form-control" placeholder="Write your thoughts here..." required></textarea>
            </div>
            <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                <button type="submit" class="btn btn-primary" style="flex: 1; padding: 1rem;">Publish Story</button>
                <a href="dashboard.php" class="btn btn-outline" style="flex: 0.5; text-align: center; padding: 1rem;">Cancel</a>
            </div>
        </form>
    </div>
</div>

<script src="script.js"></script>
</body>
</html>
