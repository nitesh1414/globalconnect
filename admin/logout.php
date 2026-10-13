<?php
/**
 * Global Connect - Admin Logout
 */
require_once __DIR__ . '/../includes/functions.php';

unset($_SESSION['admin_user_id']);
unset($_SESSION['admin_username']);
session_destroy();

session_start();
set_flash('info', 'You have been successfully logged out.');
header('Location: /admin/login.php');
exit;
