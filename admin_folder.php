<?php
require_once 'auth.php';

// Get folders ONLY from settings.json - NO automatic directory scanning
$allSettings = json_decode(file_get_contents(SETTINGS_FILE), true);
$folders = array_keys($allSettings['folders'] ?? []);

// Default fallback if no folders exist
if (empty($folders)) {
    $folders = ['iq3'];
}

$selectedFolder = isset($_GET['folder']) ? $_GET['folder'] : reset($folders);

// Validate folder exists in settings (whitelist only)
if (!in_array($selectedFolder, $folders)) {
    $selectedFolder = reset($folders);
}

$media = get_all_media($selectedFolder);
$settings = get_settings($selectedFolder);
?>
