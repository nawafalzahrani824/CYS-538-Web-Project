<?php
session_start();
require 'db.php';

// Access Control
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Access Denied: You must be an administrator to view this page. <a href='login.php'>Login here</a>");
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $file_attachment = null;

    // Handle the actual file upload
    if (isset($_FILES['file_attachment']) && $_FILES['file_attachment']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '/var/www/html/blog/attachments/';
        $fileName = basename($_FILES['file_attachment']['name']);
        $uploadPath = $uploadDir . $fileName;

        // Move the file from the temporary directory to our attachments folder
        if (move_uploaded_file($_FILES['file_attachment']['tmp_name'], $uploadPath)) {
            $file_attachment = $fileName;
        } else {
            $message = "Failed to upload file.";
        }
    }

    if (!empty($title) && !empty($content)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO posts (title, content, author_id, file_attachment) VALUES (:title, :content, :author_id, :file_attachment)");
            $stmt->execute([
                'title' => $title,
                'content' => $content,
                'author_id' => $_SESSION['user_id'],
                'file_attachment' => $file_attachment
            ]);
            $message = "Blog post published successfully with attachment!";
        } catch (PDOException $e) {
            $message = "Error publishing post: " . $e->getMessage();
        }
    } else {
        $message = "Title and Content are required.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f9; padding: 20px; }
        .container { max-width: 800px; margin: 0 auto; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        input[type="text"], textarea, input[type="file"] { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        button { background-color: #28a745; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background-color: #218838; }
        .logout { float: right; color: red; text-decoration: none; font-weight: bold; }
        .message { color: green; font-weight: bold; margin-bottom: 15px; }
    </style>
</head>
<body>

<div class="container">
    <a href="logout.php" class="logout">Logout</a>
    <h2>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?> (Admin)</h2>
    
    <h3>Create a New Blog Post</h3>
    
    <?php if ($message): ?>
        <div class="message"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <form method="POST" action="admin.php" enctype="multipart/form-data" onsubmit="return validateForm()">
        <label for="title">Post Title:</label>
        <input type="text" id="title" name="title" required>
        
        <label for="content">Post Content:</label>
        <textarea id="content" name="content" rows="6" required></textarea>
        
        <label for="file_attachment">Attach a File (Images and Text only):</label>
        <input type="file" id="file_attachment" name="file_attachment" accept=".txt, .jpg, .png">
        
        <button type="submit">Publish Post</button>
    </form>

    <script>
    function validateForm() {
        const fileInput = document.getElementById('file_attachment');
        const file = fileInput.files[0];
        
        if (file) {
            const allowedExtensions = /(\.txt|\.jpg|\.png)$/i;
            
            // Check file extension
            if (!allowedExtensions.exec(file.name)) {
                alert('Security Error: Invalid file type. Only .txt, .jpg, and .png are allowed.');
                fileInput.value = '';
                return false;
            }
            
            // Check for potential path traversal characters in the filename
            if (file.name.includes('../') || file.name.includes('..\\')) {
                alert('Security Error: Malicious filename detected.');
                fileInput.value = '';
                return false;
            }
        }
        return true;
    }
    </script>
</div>

</body>
</html>

