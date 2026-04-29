<?php
session_start();
require_once 'config.php';

function is_logged_in() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
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

if (isset($_POST['login'])) {
    $num1 = $_SESSION['admin_math_num1'] ?? 0;
    $num2 = $_SESSION['admin_math_num2'] ?? 0;
    $expected = $num1 + $num2;
    $users = get_users();
    $isValid = false;
    if ($_POST['username'] === 'admin' && $_POST['password'] === ADMIN_PASSWORD) {
        $isValid = true;
    } elseif (isset($users[$_POST['username']]) && password_verify($_POST['password'], $users[$_POST['username']])) {
        $isValid = true;
    }
    if (intval($_POST['math_answer']) != $expected) {
        $login_error = "Incorrect math answer";
    } elseif (!$isValid) {
        $login_error = "Invalid username or password";
    } else {
        $_SESSION['admin_logged_in'] = true;
        header('Location: admin.php');
        exit;
    }
}

if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: index.php');
    exit;
}
?>