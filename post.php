<?php
require_once 'db.php';

$post_id = isset($_GET['id']) ? (int)$GET['id'] : 0;

// Fetch post with author
$stmt = $pdo->prepare("
    SELECT posts.*, users.username as author, users.email as author_email
    FROM posts 
    JOIN users ON posts.user_id = users.id 
    WHERE posts.id = ?
");
$stmt->execute([$post_id]);
$post = $stmt->fetch();

if (!$post) {
    header("Location: index.php");
    exit();
}

// Handle comment submission
$comment_msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_comment'])) {
    $user_name = trim($_POST['user_name']);
    $comment_text = trim($_POST['comment_text']);

    if (!empty($user_name) && !empty($comment_text)) {
        $stmt = $pdo->prepare("INSERT INTO comments (post_id, user_name, comment_text) VALUES (?, ?, ?)");
        if ($stmt->execute([$post_id, $user_name, $comment_text])) {
            $comment_msg = "Comment added successfully!";
        }
    } else {
        $comment_msg = "Please fill in all comment fields.";
    }
}

// Fetch comments
$stmt = $pdo->prepare("SELECT * FROM comments WHERE post_id = ? ORDER BY created_at DESC");
$stmt->execute([$post_id]);
$comments = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo h($post['title']); ?> - BloggerClone</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
</head>
<body>

<header>
    <a href="index.php" class="logo">Blogger.</a>
    <nav class="nav-links">
        <a href="index.php">Home</a>
        <?php if (is_logged_in()): ?>
            <a href="dashboard.php">My Dashboard</a>
            <a href="create_post.php">New Post</a>
        <?php endif; ?>
    </nav>
    <div class="auth-btns">
        <?php if (is_logged_in()): ?>
            <span style="margin-right: 15px;">Hi, <strong><?php echo h($_SESSION['username']); ?></strong></span>
            <a href="logout.php" class="btn btn-outline btn-sm">Logout</a>
        <?php else: ?>
            <a href="login.php" style="margin-right: 15px; text-decoration: none; color: #333; font-weight: 600;">Login</a>
            <a href="signup.php" class="btn btn-primary">Sign Up</a>
        <?php endif; ?>
    </div>
</header>

<div class="post-detail-container">
    <img src="<?php echo !empty($post['image_url']) ? h($post['image_url']) : 'https://images.unsplash.com/photo-1499750310107-5fef28a66643?ixlib=rb-1.2.1&auto=format&fit=crop&w=1200&q=80'; ?>" 
         alt="<?php echo h($post['title']); ?>" class="post-detail-img">
    
    <div class="post-detail-header">
        <h1><?php echo h($post['title']); ?></h1>
        <div class="post-detail-meta">
            <span>By <strong><?php echo h($post['author']); ?></strong></span>
            <span>•</span>
            <span><?php echo date('F d, Y', strtotime($post['created_at'])); ?></span>
        </div>
    </div>

    <div class="post-detail-body">
        <?php echo nl2br(h($post['content'])); ?>
    </div>

    <div class="comments-section">
        <h3>Comments (<?php echo count($comments); ?>)</h3>
        
        <?php if ($comment_msg): ?>
            <div class="alert <?php echo strpos($comment_msg, 'successfully') !== false ? 'alert-success' : 'alert-error'; ?>" style="margin-top: 1rem;">
                <?php echo $comment_msg; ?>
            </div>
        <?php endif; ?>

        <div class="form-container" style="max-width: 100%; margin: 2rem 0; padding: 1.5rem;">
            <h4 style="margin-bottom: 1rem;">Leave a Comment</h4>
            <form method="POST">
                <div class="form-group">
                    <input type="text" name="user_name" class="form-control" placeholder="Your Name" 
                           value="<?php echo is_logged_in() ? h($_SESSION['username']) : ''; ?>" required>
                </div>
                <div class="form-group">
                    <textarea name="comment_text" class="form-control" placeholder="What are your thoughts?" style="min-height: 100px;" required></textarea>
                </div>
                <button type="submit" name="submit_comment" class="btn btn-primary">Post Comment</button>
            </form>
        </div>

        <div class="comments-list">
            <?php if (empty($comments)): ?>
                <p style="color: #888;">No comments yet. Be the first to share your thoughts!</p>
            <?php else: ?>
                <?php foreach ($comments as $comment): ?>
                    <div class="comment-card">
                        <div class="comment-author"><?php echo h($comment['user_name']); ?></div>
                        <div class="comment-date"><?php echo date('M d, Y \a\t H:i', strtotime($comment['created_at'])); ?></div>
                        <div class="comment-text"><?php echo nl2br(h($comment['comment_text'])); ?></div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="script.js"></script>
</body>
</html>
