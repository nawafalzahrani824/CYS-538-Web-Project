<?php
if (isset($_GET['file'])) {
    // COUNTERMEASURE 1: Server-Side Sanitization
    // basename() forcibly removes any directory paths (like ../../../) from the input
    $file = basename($_GET['file']);
    
    // Define the absolute base directory
    $base_dir = '/var/www/html/blog/attachments/';
    $filepath = $base_dir . $file;
    
    // COUNTERMEASURE 2: Server-Side Validation
    // realpath() resolves the absolute system path. We check if it starts strictly with our base directory.
    $real_base = realpath($base_dir);
    $real_filepath = realpath($filepath);
    
    // Verify the file exists AND resides exactly within our allowed folder
    if ($real_filepath !== false && strpos($real_filepath, $real_base) === 0 && file_exists($real_filepath)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $file . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($real_filepath));
        
        readfile($real_filepath);
        exit;
    } else {
        // If the path breaks out of the folder, block it
        die("Security Error: Invalid or malicious file request detected.");
    }
} else {
    echo "Error: No file specified.";
}
?>
