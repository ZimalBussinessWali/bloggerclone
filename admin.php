<?php
require_once __DIR__ . '/db.php';

$pdo = get_db();
$categories = get_categories();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$isEdit = $id > 0;

// Handle create/update/delete
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'save') {
        $title = trim($_POST['title'] ?? '');
        $author = trim($_POST['author'] ?? '');
        $category = trim($_POST['category'] ?? '');
        $excerpt = trim($_POST['excerpt'] ?? '');
        $content_html = $_POST['content_html'] ?? '';

        if (!in_array($category, $categories, true)) {
            $category = $categories[0];
        }

        if ($excerpt === '' && $content_html !== '') {
            $text = strip_tags($content_html);
            $excerpt = mb_substr($text, 0, 160);
        }

        if ($isEdit) {
            $stmt = $pdo->prepare('UPDATE posts SET title = :title, author = :author, category = :category, excerpt = :excerpt, content_html = :content_html WHERE id = :id');
            $stmt->execute([
                ':title' => $title,
                ':author' => $author,
                ':category' => $category,
                ':excerpt' => $excerpt,
                ':content_html' => $content_html,
                ':id' => $id,
            ]);
        } else {
            $stmt = $pdo->prepare('INSERT INTO posts (title, author, category, excerpt, content_html) VALUES (:title, :author, :category, :excerpt, :content_html)');
            $stmt->execute([
                ':title' => $title,
                ':author' => $author,
                ':category' => $category,
                ':excerpt' => $excerpt,
                ':content_html' => $content_html,
            ]);
            $id = (int)$pdo->lastInsertId();
            $isEdit = true;
        }

        header('Location: post.php?id=' . $id);
        exit;
    } elseif ($action === 'delete' && $isEdit) {
        $stmt = $pdo->prepare('DELETE FROM posts WHERE id = :id');
        $stmt->execute([':id' => $id]);
        header('Location: index.php');
        exit;
    }
}

$post = null;
if ($isEdit) {
    $stmt = $pdo->prepare('SELECT * FROM posts WHERE id = :id');
    $stmt->execute([':id' => $id]);
    $post = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$post) {
        http_response_code(404);
        echo 'Post not found';
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $isEdit ? 'Edit Post' : 'New Post'; ?> - BloggerClone</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <header class="site-header">
        <div class="container header-inner">
            <a href="index.php" class="logo">BloggerClone</a>
            <nav class="nav">
                <?php if ($isEdit): ?>
                    <a href="post.php?id=<?php echo (int)$post['id']; ?>" class="button">View</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <main class="container">
        <h1><?php echo $isEdit ? 'Edit Post' : 'Create New Post'; ?></h1>
        <form id="post-form" class="post-form" method="post" action="admin.php<?php echo $isEdit ? ('?id=' . (int)$post['id']) : ''; ?>">
            <input type="hidden" name="action" value="save" />
            <div class="form-row">
                <label>Title</label>
                <input type="text" name="title" required value="<?php echo htmlspecialchars($post['title'] ?? ''); ?>"/>
            </div>
            <div class="form-row">
                <label>Author</label>
                <input type="text" name="author" required value="<?php echo htmlspecialchars($post['author'] ?? ''); ?>"/>
            </div>
            <div class="form-row">
                <label>Category</label>
                <select name="category" required>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo htmlspecialchars($cat); ?>" <?php echo (($post['category'] ?? '') === $cat) ? 'selected' : ''; ?>><?php echo htmlspecialchars($cat); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-row">
                <label>Excerpt (optional)</label>
                <textarea name="excerpt" rows="3" placeholder="Short summary..."><?php echo htmlspecialchars($post['excerpt'] ?? ''); ?></textarea>
            </div>
            <div class="form-row">
                <label>Content</label>
                <div id="editor" class="editor" contenteditable="true"><?php echo $post ? $post['content_html'] : '<p>Write your story...</p>'; ?></div>
                <textarea id="content_html" name="content_html" class="hidden-textarea"><?php echo htmlspecialchars($post['content_html'] ?? ''); ?></textarea>
                <p class="muted">Use the toolbar for basic formatting. Paste images/links directly.</p>
                <div class="toolbar">
                    <button type="button" data-cmd="bold"><strong>B</strong></button>
                    <button type="button" data-cmd="italic"><em>I</em></button>
                    <button type="button" data-cmd="underline"><u>U</u></button>
                    <button type="button" data-cmd="formatBlock" data-value="H2">H2</button>
                    <button type="button" data-cmd="formatBlock" data-value="H3">H3</button>
                    <button type="button" data-cmd="insertUnorderedList">• List</button>
                    <button type="button" data-cmd="createLink">Link</button>
                </div>
            </div>
            <div class="form-actions">
                <button type="submit" class="button primary">Save</button>
                <?php if ($isEdit): ?>
                    <button type="submit" name="action" value="delete" class="button danger" onclick="return confirm('Delete this post?');">Delete</button>
                <?php endif; ?>
            </div>
        </form>
    </main>

    <footer class="site-footer">
        <div class="container">
            <p>© <?php echo date('Y'); ?> BloggerClone</p>
        </div>
    </footer>

    <script src="assets/app.js"></script>
    </body>
    </html>


