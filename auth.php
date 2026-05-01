<?php
session_start();
require_once 'config.php';

function is_logged_in() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

function is_admin_account() {
    return is_logged_in() && isset($_SESSION['admin_username']) && $_SESSION['admin_username'] === 'admin';
}

function add_log($action, $details = '') {
    $logFile = 'logs.json';
    $logs = [];
    
    if (file_exists($logFile)) {
        $content = file_get_contents($logFile);
        $logs = json_decode($content, true) ?: [];
    }
    
    // Auto clean logs older than 30 days
    $thirtyDaysAgo = time() - (30 * 24 * 60 * 60);
    $logs = array_filter($logs, function($entry) use ($thirtyDaysAgo) {
        return $entry['timestamp'] > $thirtyDaysAgo;
    });
    
    // Keep maximum 1000 entries
    if (count($logs) >= 1000) {
        array_shift($logs);
    }
    
    $logs[] = [
        'timestamp' => time(),
        'datetime' => date('Y-m-d H:i:s'),
        'username' => $_SESSION['admin_username'] ?? 'unknown',
        'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
        'action' => $action,
        'details' => $details
    ];
    
    file_put_contents($logFile, json_encode(array_values($logs), JSON_PRETTY_PRINT));
    @chmod($logFile, 0600);
}

function get_all_logs() {
    $logFile = 'logs.json';
    if (!file_exists($logFile)) return [];
    $content = file_get_contents($logFile);
    return json_decode($content, true) ?: [];
}

function require_login() {
    if (!is_logged_in()) {
        header('Location: admin.php');
        exit;
    }
}

function is_user_logged_in() {
    return isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true;
}

function require_user_login() {
    if (!is_user_logged_in()) {
        header('Location: index.php');
        exit;
    }
}

function user_login($username, $password, $math_answer, $expected) {
    if (intval($math_answer) != $expected) {
        return "Incorrect math answer";
    }
    $users = get_users();
    if (!isset($users[$username]) || !password_verify($password, $users[$username])) {
        return "Invalid username or password";
    }
    $_SESSION['user_logged_in'] = true;
    return true;
}

function user_logout() {
    unset($_SESSION['user_logged_in']);
}

function process_admin_login() {
    if (!isset($_POST['login'])) {
        return;
    }

    $_SESSION['login_username'] = $_POST['username'] ?? '';
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // First validate credentials
    $users = get_users();
    $validCredentials = false;

    if ($username === 'admin' && $password === ADMIN_PASSWORD) {
        $validCredentials = true;
    } elseif (isset($users[$username]) && password_verify($password, $users[$username])) {
        $validCredentials = true;
    }

    if (!$validCredentials) {
        $_SESSION['login_error'] = '❌ Invalid username or password.';
        // Generate NEW CAPTCHA
        unset($_SESSION['admin_math_num1']);
        unset($_SESSION['admin_math_num2']);
        add_log('LOGIN_FAILED', "Invalid credentials for user: $username");
        header('Location: admin.php');
        exit;
    }

    // Only if credentials are correct - check math answer
    if (!isset($_POST['math_answer']) || intval($_POST['math_answer']) != ($_SESSION['admin_math_num1'] + $_SESSION['admin_math_num2'])) {
        $_SESSION['login_error'] = '❌ Wrong captcha. Please try again.';
        // Generate NEW CAPTCHA
        unset($_SESSION['admin_math_num1']);
        unset($_SESSION['admin_math_num2']);
        add_log('LOGIN_FAILED', "Wrong captcha for user: $username");
    } else {
        // Both correct - login success
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $username;
        // Clear math captcha
        unset($_SESSION['admin_math_num1']);
        unset($_SESSION['admin_math_num2']);
        
        add_log('LOGIN_SUCCESS', 'User logged in');
    }

    header('Location: admin.php');
    exit;
}

// Run login processing automatically when admin.php loads
if (basename($_SERVER['PHP_SELF']) === 'admin.php' && isset($_POST['login'])) {
    process_admin_login();
}

if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: index.php');
    exit;
}
?>
