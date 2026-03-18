<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

session_start();

unset($_SESSION['user_id']);
unset($_SESSION['username']);
unset($_SESSION['role']);
unset($_SESSION['full_name']);

if (isset($_COOKIE['remember_me'])) {
    setcookie('remember_me', '', time() - 3600, '/');
}

session_destroy();

redirect('login.php');