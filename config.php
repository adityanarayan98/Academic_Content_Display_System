<?php
// Configuration File - Edit these settings
define('ADMIN_PASSWORD', 'Aditya@2026'); // Change this to your secure password
define('IMAGE_FOLDER', 'iq3');
define('MAX_FILE_SIZE', 500 * 1024 * 1024); // 500MB
define('ALLOWED_TYPES', ['jpg', 'jpeg', 'png', 'gif']);
define('SETTINGS_FILE', __DIR__ . '/settings.json');
define('TEMP_FOLDER', __DIR__ . '/temp');

// Auto create temp folder if not exists
if (!is_dir(TEMP_FOLDER)) {
    mkdir(TEMP_FOLDER, 0755, true);
}

// Initialize settings file if not exists
if (!file_exists(SETTINGS_FILE)) {
    file_put_contents(SETTINGS_FILE, json_encode([
        'folders' => [
            'iq3' => [
                'timer' => 5,
                'sequence' => [],
                'orientation' => 'landscape'
            ]
        ]
    ]));
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
    // Only allow folders that exist in settings
    if (!is_folder_allowed($folder)) {
        $folder = reset(get_allowed_folders());
    }
    
    $allSettings = json_decode(file_get_contents(SETTINGS_FILE), true);
    if (!isset($allSettings['folders'][$folder])) {
        $allSettings['folders'][$folder] = ['timer' => 5, 'sequence' => [], 'orientation' => 'landscape'];
        file_put_contents(SETTINGS_FILE, json_encode($allSettings, JSON_PRETTY_PRINT));
    } else {
        // Ensure orientation is set for existing folders
        if (!isset($allSettings['folders'][$folder]['orientation'])) {
            $allSettings['folders'][$folder]['orientation'] = 'landscape';
            file_put_contents(SETTINGS_FILE, json_encode($allSettings, JSON_PRETTY_PRINT));
        }
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
    $media = glob($folderPath . '/*.{jpg,jpeg,png,gif,mp4,avi,mov,wmv}', GLOB_BRACE);
    if (!is_array($media)) {
        $media = [];
    }

    $settings = get_settings($folder);
    $savedSequence = $settings['sequence'];

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
