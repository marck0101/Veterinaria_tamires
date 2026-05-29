<?php
ini_set('session.save_path', '/tmp');
session_start();
session_destroy();
header('Location: /admin/login.php');
exit;