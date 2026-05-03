<?php
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
$host = $_SERVER['HTTP_HOST'];
$isLocal = (
    strpos($host, 'localhost') !== false ||
    strpos($host, '127.0.0.1') !== false ||
    strpos($host, '192.168') !== false
);

$subFolder = $isLocal ? '/app/view' : '';

define('URL', $protocol . '://' . $host . $subFolder);
define('URLs', $protocol . '://' . $host);
if (session_status() === PHP_SESSION_NONE) session_start();
ini_set('memory_limit', '256M');
ini_set('display_errors', '0');
ini_set('log_errors', '1');
error_reporting(E_ALL);

require_once 'app/model/Env.php';

Env::load(__DIR__ . '/.env');
