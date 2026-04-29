<?php
require_once 'auth.php';

$folders = [];
$dir = opendir('.');
while (($file = readdir($dir)) !== false) {
    if ($file !== '.' && $file !== '..' && is_dir($file) && !in_array($file, ['.kilo', '.kilocode'])) {
        $folders[] = $file;
    }
}
closedir($dir);

$selectedFolder = isset($_GET['folder']) ? $_GET['folder'] : 'iq3';
$media = get_all_media($selectedFolder);
$settings = get_settings($selectedFolder);
?>