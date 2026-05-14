<?php
if (php_sapi_name() === 'cli-server') {
    $file = __DIR__ . parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
    if (is_file($file))
        return false;
}
ob_start();
header('Content-Type: text/html; charset=UTF-8');
define('APP', true);
if (session_status() === PHP_SESSION_NONE)
    session_start();
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$staticDirs = ['css', 'js', 'img', 'fonts', 'pdf'];

foreach ($staticDirs as $dir) {
    if (strpos($path, "/$dir/") !== false) {
        $base = realpath(__DIR__ . '/app/view');

        $file = realpath($base . $path);

        if ($file && str_starts_with($file, $base) && is_file($file)) {
            $ext = pathinfo($file, PATHINFO_EXTENSION);
            $mime = [
                'css' => 'text/css; charset=UTF-8',
                'js' => 'application/javascript; charset=UTF-8',
                'pdf' => 'application/pdf',
                'png' => 'image/png',
                'jpg' => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'gif' => 'image/gif',
                'svg' => 'image/svg+xml',
            ];

            header('Content-Type: ' . ($mime[$ext] ?? 'application/octet-stream'));
            readfile($file);
            exit;
        }
    }
}
require_once __DIR__ . '/bootstrap.php';
$uri = rtrim($path, '/');
if ($uri === '') $uri = '/';
$HomeGenciador = __DIR__ . '/app/view/vendor/layout/main.php';
$AddForm = __DIR__ . '/app/view/vendor/formularioa/formAdd.php';
$currentTable = null;

$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';

$fileRoutes = [
    '/' => __DIR__ . '/app/view/site.php',
    '/PengueMonte' => $HomeGenciador,
    '/carinho' => $isAjax ? __DIR__ . "/app/view/vendor/carinho/carinho.php" : $HomeGenciador,
    '/form' => $isAjax ? __DIR__ . '/app/view/vendor/formularioa/local.php' : $HomeGenciador,
    '/categorias' =>  $isAjax ? $AddForm : $HomeGenciador,
    '/produtos' =>  $isAjax ? $AddForm : $HomeGenciador,
    // '/categorias' =>   $AddForm,
    // '/produtos' =>   $AddForm,
];
if ($uri === '/listprodutos') {
    $controller = new ProductController();
    $controller->outputJson();
    exit;
}
if (isset($fileRoutes[$uri])) {
    $currentTable = ltrim($uri, '/');
    require $fileRoutes[$uri];
    exit;
}
if ($uri === '/processCategoria') {
    if (!categorias::inserirDados()) header('location: /categorias');
    else header('location: /carinho');
    exit;
}
