<?php
require_once 'db.php';
require_login();

$post_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$user_id = $_SESSION['user_id'];

// Check if post exists and belongs to user
$stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ? AND user_id = ?");
$stmt->execute([$post_id, $user_id]);
$post = $stmt->fetch();

if (!$post) {
    header("Location: dashboard.php");
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $image_url = trim($_POST['image_url']);

    if (empty($title) || empty($content)) {
        $error = "Title and content are required.";
    } else {
        $stmt = $pdo->prepare("UPDATE posts SET title = ?, content = ?, image_url = ? WHERE id = ? AND user_id = ?");
        if ($stmt->execute([$title, $content, $image_url, $post_id, $user_id])) {
            $_SESSION['msg'] = "Post updated successfully!";
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Failed to update post.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Post - BloggerClone</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
</head>
<body>

<header>
    <a href="index.php" class="logo">Blogger.</a>
    <nav class="nav-links">
        <a href="index.php">Home</a>
        <a href="dashboard.php">My Dashboard</a>
        <a href="create_post.php">New Post</a>
    </nav>
    <div class="auth-btns">
        <span style="margin-right: 15px;">Hi, <strong><?php echo h($_SESSION['username']); ?></strong></span>
        <a href="logout.php" class="btn btn-outline btn-sm">Logout</a>
    </div>
</header>

<div class="container" style="max-width: 900px; margin-top: 3rem;">
    <div class="dashboard-header">
        <h1>Edit Your Story</h1>
        <a href="dashboard.php" class="btn btn-outline btn-sm">Back to Dashboard</a>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>

    <div class="form-container" style="max-width: 100%; margin: 0;">
        <form method="POST" action="edit_post.php?id=<?php echo $post_id; ?>">
            <div class="form-group">
                <label class="form-label">Post Title</label>
                <input type="text" name="title" class="form-control" value="<?php echo h($post['title']); ?>" required>
            </div>
            <div class="form-group">
                <label class="form-label">Featured Image URL (Optional)</label>
                <input type="url" name="image_url" class="form-control" value="<?php echo h($post['image_url']); ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Story Content</label>
                <textarea name="content" class="form-control" required><?php echo h($post['content']); ?></textarea>
            </div>
            <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                <button type="submit" class="btn btn-primary" style="flex: 1; padding: 1rem;">Update Story</button>
                <a href="dashboard.php" class="btn btn-outline" style="flex: 0.5; text-align: center; padding: 1rem;">Discard Changes</a>
            </div>
        </form>
    </div>
</div>

<script src="script.js"></script>
</body>
</html>
