<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
$root = __DIR__;
require_once $root . '/vendor/autoload.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$_GET = [];
$_SERVER['REQUEST_METHOD'] = 'GET';
ob_start();
$controller = new App\Controllers\AvaliacaoController();
$controller->listarPorMidia();
$output = ob_get_clean();
echo '[[[STATUS]]]' . http_response_code() . "\n" . $output;
