<?php
require_once 'config.php';

$folder = isset($_GET['folder']) ? $_GET['folder'] : 'iq3';
$media = get_all_media($folder);
$settings = get_settings($folder);

header('Content-Type: application/json');
echo json_encode(['media' => $media, 'settings' => $settings]);
?>