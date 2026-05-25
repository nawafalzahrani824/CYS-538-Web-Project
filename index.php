<?php
require 'db.php';

// Fetch all posts and join with the users table to get the author's username
try {
    $stmt = $pdo->query("SELECT posts.*, users.username FROM posts JOIN users ON posts.author_id = users.id ORDER BY created_at DESC");
    $posts = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Error fetching posts: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>CYS 538 Cybersecurity Blog</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f9; padding: 20px; }
        .container { max-width: 800px; margin: 0 auto; }
        .header { display: flex; justify-content: space-between; align-items: center; background: #0056b3; color: white; padding: 15px 20px; border-radius: 8px; margin-bottom: 20px; }
        .header a { color: white; text-decoration: none; font-weight: bold; background: #004494; padding: 8px 12px; border-radius: 4px; }
        .post { background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .post h2 { margin-top: 0; }
        .meta { font-size: 0.9em; color: #666; margin-bottom: 15px; }
        .attachment { margin-top: 15px; padding: 10px; background: #e9ecef; border-left: 4px solid #0056b3; }
        .attachment a { font-weight: bold; color: #0056b3; text-decoration: none; }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h1>Welcome to the Blog</h1>
        <a href="login.php">Admin Login</a>
    </div>

    <?php if (count($posts) > 0): ?>
        <?php foreach ($posts as $post): ?>
            <div class="post">
                <h2><?php echo htmlspecialchars($post['title']); ?></h2>
                <div class="meta">Posted by <?php echo htmlspecialchars($post['username']); ?> on <?php echo $post['created_at']; ?></div>
                <p><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>
                
                <?php if (!empty($post['file_attachment'])): ?>
                    <div class="attachment">
                        Attachment: 
                        <a href="download.php?file=<?php echo urlencode($post['file_attachment']); ?>">
                            Download <?php echo htmlspecialchars($post['file_attachment']); ?>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No posts found. Log in as admin to create one!</p>
    <?php endif; ?>
</div>

</body>
</html>
