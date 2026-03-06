<?php
require_once 'db.php';

// Fetch all posts with author names
$stmt = $pdo->query("
    SELECT posts.*, users.username as author 
    FROM posts 
    JOIN users ON posts.user_id = users.id 
    ORDER BY posts.created_at DESC
");
$posts = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BloggerClone - Share Your Thoughts</title>
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

<section class="hero">
    <h1>Welcome to Blogger Clone</h1>
    <p>A place where world-class creators share their unique perspectives and stories.</p>
    <?php if (!is_logged_in()): ?>
        <a href="signup.php" class="btn btn-primary">Start Writing Today</a>
    <?php endif; ?>
</section>

<div class="container">
    <div class="dashboard-header">
        <h2>Latest Stories</h2>
    </div>

    <div class="post-grid">
        <?php if (empty($posts)): ?>
            <p style="grid-column: 1/-1; text-align: center; color: #666; padding: 3rem;">No posts yet. Be the first to share a story!</p>
        <?php else: ?>
            <?php foreach ($posts as $post): ?>
                <article class="post-card">
                    <img src="<?php echo !empty($post['image_url']) ? h($post['image_url']) : 'https://images.unsplash.com/photo-1499750310107-5fef28a66643?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80'; ?>" 
                         alt="<?php echo h($post['title']); ?>" class="post-img">
                    <div class="post-content">
                        <h3 class="post-title"><?php echo h($post['title']); ?></h3>
                        <p class="post-excerpt"><?php echo h(substr(strip_tags($post['content']), 0, 150)) . '...'; ?></p>
                        <div class="post-meta">
                            <span class="post-author">By <?php echo h($post['author']); ?></span>
                            <span class="post-date"><?php echo date('M d, Y', strtotime($post['created_at'])); ?></span>
                        </div>
                        <a href="post.php?id=<?php echo $post['id']; ?>" class="btn btn-primary btn-sm" style="margin-top: 1rem; text-align: center;">Read More</a>
                    </div>
                </article>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<script src="script.js"></script>
</body>
</html>
