<?php
require_once __DIR__ . '/../includes/config.php';

session_destroy();
header('Location: ' . base_url('admin/login.php'));
exit();
