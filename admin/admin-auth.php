<?php
ini_set('session.save_path', '/tmp');
ini_set('session.cookie_path', '/');
session_start();

if (!isset($_SESSION['admin_id'])) {
    header('Location: /admin/login.php');
    exit;
}