<?php
require_once 'db.php';
require_login();

$user_id = $_SESSION['user_id'];

// Handle deletion
if (isset($_GET['delete'])) {
    $post_id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM posts WHERE id = ? AND user_id = ?");
    $stmt->execute([$post_id, $user_id]);
    $_SESSION['msg'] = "Post deleted successfully.";
    header("Location: dashboard.php");
    exit();
}

// Fetch user's posts
$stmt = $pdo->prepare("SELECT * FROM posts WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$my_posts = $stmt->fetchAll();

$msg = isset($_SESSION['msg']) ? $_SESSION['msg'] : '';
unset($_SESSION['msg']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Dashboard - BloggerClone</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
</head>
<body>

<header>
    <a href="index.php" class="logo">Blogger.</a>
    <nav class="nav-links">
        <a href="index.php">Home</a>
        <a href="dashboard.php" style="color: var(--primary-color);">My Dashboard</a>
        <a href="create_post.php">New Post</a>
    </nav>
    <div class="auth-btns">
        <span style="margin-right: 15px;">Hi, <strong><?php echo h($_SESSION['username']); ?></strong></span>
        <a href="logout.php" class="btn btn-outline btn-sm">Logout</a>
    </div>
</header>

<div class="container" style="margin-top: 3rem;">
    <div class="dashboard-header">
        <h1>My Dashboard</h1>
        <a href="create_post.php" class="btn btn-primary">+ Create New Post</a>
    </div>

    <?php if ($msg): ?>
        <div class="alert alert-success"><?php echo $msg; ?></div>
    <?php endif; ?>

    <div style="background: white; border-radius: 20px; box-shadow: var(--shadow); overflow: hidden;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Published Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($my_posts)): ?>
                    <tr>
                        <td colspan="3" style="text-align: center; padding: 3rem; color: #666;">
                            You haven't written any posts yet. <a href="create_post.php" style="color: var(--primary-color); font-weight: 600;">Start writing!</a>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($my_posts as $post): ?>
                        <tr>
                            <td>
                                <div style="font-weight: 700; color: #1a1a1a;"><?php echo h($post['title']); ?></div>
                                <div style="font-size: 0.85rem; color: #888; margin-top: 4px;"><?php echo h(substr(strip_tags($post['content']), 0, 60)) . '...'; ?></div>
                            </td>
                            <td><?php echo date('M d, Y', strtotime($post['created_at'])); ?></td>
                            <td class="actions">
                                <a href="post.php?id=<?php echo $post['id']; ?>" class="btn btn-outline btn-sm">View</a>
                                <a href="edit_post.php?id=<?php echo $post['id']; ?>" class="btn btn-primary btn-sm">Edit</a>
                                <a href="dashboard.php?delete=<?php echo $post['id']; ?>" class="btn btn-danger btn-sm btn-confirm-delete">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="script.js"></script>
</body>
</html>
