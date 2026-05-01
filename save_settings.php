<?php
require_once 'auth.php';
require_login();

if (isset($_POST['set_admin_folder'])) {
    $_SESSION['admin_folder'] = $_POST['set_admin_folder'];
    echo json_encode(['success' => true]);
    exit;
}

if (isset($_POST['add_user']) || isset($_POST['update_user'])) {
    if (!is_admin_account()) {
        add_log('PERMISSION_DENIED', "Attempted user management access denied");
        http_response_code(403);
        echo json_encode(['error' => 'Only administrator can manage users']);
        exit;
    }
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    if (empty($username) || empty($password)) {
        http_response_code(400);
        echo json_encode(['error' => 'Username and password required']);
        exit;
    }
    $users = get_users();
    $isUpdate = isset($_POST['update_user']);
    if (!$isUpdate && isset($users[$username])) {
        http_response_code(400);
        echo json_encode(['error' => 'User already exists']);
        exit;
    }
    if ($isUpdate && !isset($users[$username])) {
        http_response_code(400);
        echo json_encode(['error' => 'User not found']);
        exit;
    }
    save_user($username, $password);
    add_log(($isUpdate ? 'USER_UPDATED' : 'USER_CREATED'), "User: $username");
    echo json_encode(['success' => true]);
    exit;
}

if (isset($_POST['delete_user'])) {
    if (!is_admin_account()) {
        add_log('PERMISSION_DENIED', "Attempted user deletion access denied");
        http_response_code(403);
        echo json_encode(['error' => 'Only administrator can manage users']);
        exit;
    }
    $username = trim($_POST['delete_user']);
    $allSettings = json_decode(file_get_contents(SETTINGS_FILE), true);
    if (isset($allSettings['users'][$username])) {
        unset($allSettings['users'][$username]);
        file_put_contents(SETTINGS_FILE, json_encode($allSettings, JSON_PRETTY_PRINT));
        add_log('USER_DELETED', "User: $username");
        echo json_encode(['success' => true]);
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'User not found']);
    }
    exit;
}

$folder = isset($_POST['folder']) ? $_POST['folder'] : 'iq3';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid request']);
    exit;
}

$settings = get_settings($folder);
$updated = false;

// Timer
if (isset($_POST['timer'])) {
    $timer = intval($_POST['timer']);
    if ($timer < 1 || $timer > 60) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid timer value']);
        exit;
    }
    $settings['timer'] = $timer;
    $updated = true;
}

// Orientation
if (isset($_POST['orientation'])) {
    $orientation = $_POST['orientation'];
    if (!in_array($orientation, ['landscape', 'portrait'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid orientation']);
        exit;
    }
    $settings['orientation'] = $orientation;
    $updated = true;
}

// Sequence
if (isset($_POST['sequence'])) {
    $sequence = $_POST['sequence'];
    if (!is_array($sequence)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid sequence']);
        exit;
    }
    $settings['sequence'] = $sequence;
    $updated = true;
}

// Nothing sent
if (!$updated) {
    http_response_code(400);
    echo json_encode(['error' => 'No valid data provided']);
    exit;
}

save_settings($folder, $settings);

$changed = [];
if (isset($_POST['timer'])) $changed[] = "timer={$_POST['timer']}s";
if (isset($_POST['orientation'])) $changed[] = "orientation={$_POST['orientation']}";
if (isset($_POST['sequence'])) $changed[] = "order_saved";

add_log('SETTINGS_UPDATED', "Folder: $folder - " . implode(', ', $changed));

echo json_encode(['success' => true]);
