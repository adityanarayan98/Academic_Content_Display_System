<?php
// Configuration File - Edit these settings
define('ADMIN_PASSWORD', 'Aditya'); // Change this to your secure password
define('IMAGE_FOLDER', 'iq3');
define('MAX_FILE_SIZE', 500 * 1024 * 1024); // 500MB
define('ALLOWED_TYPES', ['jpg', 'jpeg', 'png', 'gif']);
define('SETTINGS_FILE', __DIR__ . '/settings.json');
define('TEMP_FOLDER', __DIR__ . '/temp');

// Auto create temp folder if not exists
if (!is_dir(TEMP_FOLDER)) {
    mkdir(TEMP_FOLDER, 0755, true);
}

// Initialize settings file if not exists or corrupted
$defaultSettings = [
    'folders' => [
        'iq3' => [
            'timer' => 5,
            'sequence' => [],
            'orientation' => 'landscape'
        ]
    ]
];

if (!file_exists(SETTINGS_FILE)) {
    file_put_contents(SETTINGS_FILE, json_encode($defaultSettings, JSON_PRETTY_PRINT));
} else {
    // Validate and recover from corrupted settings.json
    $settingsContent = file_get_contents(SETTINGS_FILE);
    $testDecode = json_decode($settingsContent, true);
    if (json_last_error() !== JSON_ERROR_NONE || !is_array($testDecode)) {
        // Backup corrupted file
        copy(SETTINGS_FILE, SETTINGS_FILE . '.backup.' . time());
        // Restore default settings
        file_put_contents(SETTINGS_FILE, json_encode($defaultSettings, JSON_PRETTY_PRINT));
    }
}

function get_allowed_folders() {
    $allSettings = json_decode(file_get_contents(SETTINGS_FILE), true);
    $folders = array_keys($allSettings['folders'] ?? []);
    return empty($folders) ? ['iq3'] : $folders;
}

function is_folder_allowed($folder) {
    return in_array($folder, get_allowed_folders());
}

function get_settings($folder = 'iq3') {
    $allSettings = json_decode(file_get_contents(SETTINGS_FILE), true);
    
    // CREATE NEW FOLDER FIRST IF IT DOES NOT EXIST
    if (!isset($allSettings['folders'][$folder])) {
        $allSettings['folders'][$folder] = ['timer' => 5, 'sequence' => [], 'orientation' => 'landscape'];
        file_put_contents(SETTINGS_FILE, json_encode($allSettings, JSON_PRETTY_PRINT));
        
        // Auto create physical folder
        if (!is_dir($folder)) {
            mkdir($folder, 0755, true);
        }
    }

    // NOW run validation - folder is already saved
    if (!is_folder_allowed($folder)) {
        $allowedFolders = get_allowed_folders();
        $folder = reset($allowedFolders);
    }
    
    // Ensure orientation is set for existing folders
    if (!isset($allSettings['folders'][$folder]['orientation'])) {
        $allSettings['folders'][$folder]['orientation'] = 'landscape';
        file_put_contents(SETTINGS_FILE, json_encode($allSettings, JSON_PRETTY_PRINT));
    }
    return $allSettings['folders'][$folder];
}

function save_settings($folder, $settings) {
    $allSettings = json_decode(file_get_contents(SETTINGS_FILE), true);
    $allSettings['folders'][$folder] = $settings;
    file_put_contents(SETTINGS_FILE, json_encode($allSettings, JSON_PRETTY_PRINT));
}

function get_all_media($folder = 'iq3') {
    $folderPath = $folder;
    // Case insensitive search to match both .jpg and .JPG extensions
    $media = glob($folderPath . '/*.{jpg,JPG,jpeg,JPEG,png,PNG,gif,GIF,mp4,MP4,avi,AVI,mov,MOV,wmv,WMV}', GLOB_BRACE | GLOB_NOSORT);
    if (!is_array($media)) {
        $media = [];
    }

    $settings = get_settings($folder);
    $savedSequence = $settings['sequence'];
    
    // Auto clean sequence array - remove entries for files that no longer exist
    $existingFiles = array_map('basename', $media);
    $cleanSequence = [];
    foreach ($savedSequence as $filename) {
        if (in_array($filename, $existingFiles)) {
            $cleanSequence[] = $filename;
        }
    }
    
    // Update sequence if it was cleaned
    if (count($cleanSequence) !== count($savedSequence)) {
        $settings['sequence'] = $cleanSequence;
        save_settings($folder, $settings);
        $savedSequence = $cleanSequence;
    }

    if (!empty($savedSequence)) {
        usort($media, function($a, $b) use ($savedSequence) {
            $posA = array_search(basename($a), $savedSequence);
            $posB = array_search(basename($b), $savedSequence);
            if ($posA === false) $posA = 9999;
            if ($posB === false) $posB = 9999;
            return $posA - $posB;
        });
    }

    return $media;
}

function get_users() {
    $allSettings = json_decode(file_get_contents(SETTINGS_FILE), true);
    return $allSettings['users'] ?? [];
}

function save_user($username, $password) {
    $allSettings = json_decode(file_get_contents(SETTINGS_FILE), true);
    $allSettings['users'][$username] = password_hash($password, PASSWORD_DEFAULT);
    file_put_contents(SETTINGS_FILE, json_encode($allSettings, JSON_PRETTY_PRINT));
}
?>
