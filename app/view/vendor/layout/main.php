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
        <link rel=" icon" href="data:," type="image/x-icon" />
        <link rel="stylesheet" href="/app/view/vendor/layout/css/main.css">
        <link rel="stylesheet" href="/app/view/vendor/layout/css/footer.css">
        <link rel="stylesheet" href="/app/view/vendor/layout/css/hearder.css">
        <title>loja</title>
    </head>

    <body>
    <?php
    require_once __DIR__ . '/../../../../icon/newiconbase.html';
    require_once __DIR__ . '/../../../../app/view/vendor/layout/Top.php';
} ?>
    <main class="content">
        <?php
        $uri = rtrim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
        if ($uri === "/carinho") {
            require __DIR__ . "/../carinho/carinho.php";
        } else {
            echo "<div class='bem-vindo'><h1>Bem-vindo ao Agendamento de Sala</h1></div>";
        }
        ?>
    </main>
    <?php if (!$isAjax) {
        require_once __DIR__ . '/../../../../app/view/vendor/layout/footer.php'; ?>
    </body>

    </html>
<?php } ?>