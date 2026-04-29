<?php
require_once 'auth.php';
require_login();

if (isset($_POST['file'])) {
    $file = $_POST['file'];

    // Prevent path traversal
    if (strpos($file, '..') !== false || strpos($file, '/') === 0) {
        http_response_code(400);
        exit("Invalid path");
    }

    // Extract folder and basename
    $folder = dirname($file);
    $basename = basename($file);

    // Load settings for the folder
    $settings = get_settings($folder);

    // Remove from sequence if present
    if (($key = array_search($basename, $settings['sequence'])) !== false) {
        unset($settings['sequence'][$key]);
        $settings['sequence'] = array_values($settings['sequence']); // Reindex
        save_settings($folder, $settings);
    }

    if (file_exists($file) && is_file($file)) {
        unlink($file);
        echo "OK";
    } else {
        http_response_code(404);
        exit("File not found");
    }
}
?>