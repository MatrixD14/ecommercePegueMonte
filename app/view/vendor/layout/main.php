<?php
defined('APP') or die('Acesso negado');
if (session_status() === PHP_SESSION_NONE) session_start();
$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';
if (!$isAjax) {
?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Document</title>
    </head>

    <body>
    <?php
    require_once __DIR__ . '/../../../../app/view/vendor/layout/Top.php';
} ?>
    </body>

    </html>