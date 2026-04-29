<?php
require_once 'auth.php';
require_login();

$folder = isset($_POST['folder']) ? $_POST['folder'] : 'iq3';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_FILES['image'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid request']);
    exit;
}

$files = $_FILES['image'];
$uploadDir = $folder . '/';

// Create directory if it doesn't exist
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

$uploadedFiles = [];
$errors = [];

if (is_array($files['name'])) {
    // Multiple files
    $fileCount = count($files['name']);
    for ($i = 0; $i < $fileCount; $i++) {
        $file = [
            'name' => $files['name'][$i],
            'type' => $files['type'][$i],
            'tmp_name' => $files['tmp_name'][$i],
            'error' => $files['error'][$i],
            'size' => $files['size'][$i]
        ];

        // Skip empty
        if ($file['error'] === UPLOAD_ERR_NO_FILE) continue;

        // Validate
        $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($fileExtension, ALLOWED_TYPES)) {
            $errors[] = $file['name'] . ': Invalid file type';
            continue;
        }

        if ($file['size'] > MAX_FILE_SIZE) {
            $errors[] = $file['name'] . ': File too large';
            continue;
        }

        // Generate unique filename
        $uniqueName = uniqid() . '.' . $fileExtension;
        $targetPath = $uploadDir . $uniqueName;

        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            $uploadedFiles[] = $uniqueName;
        } else {
            $errors[] = $file['name'] . ': Upload failed';
        }
    }
} else {
    // Single file (fallback)
    $file = $files;
    if ($file['error'] !== UPLOAD_ERR_NO_FILE) {
        $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($fileExtension, ALLOWED_TYPES)) {
            $errors[] = 'Invalid file type';
        } elseif ($file['size'] > MAX_FILE_SIZE) {
            $errors[] = 'File too large';
        } else {
            $uniqueName = uniqid() . '.' . $fileExtension;
            $targetPath = $uploadDir . $uniqueName;
            if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                $uploadedFiles[] = $uniqueName;
            } else {
                $errors[] = 'Upload failed';
            }
        }
    }
}

// Update sequence with all uploaded files
if (!empty($uploadedFiles)) {
    $settings = get_settings($folder);
    $settings['sequence'] = array_merge($settings['sequence'], $uploadedFiles);
    save_settings($folder, $settings);
}

if (empty($errors)) {
    echo json_encode(['success' => true, 'uploaded' => count($uploadedFiles)]);
} else {
    http_response_code(400);
    echo json_encode(['error' => implode('; ', $errors)]);
}
?>