<?php
error_reporting(E_ALL);
ini_set('display_errors',1);
$root = __DIR__;
require_once $root . '/vendor/autoload.php';
if (session_status() === PHP_SESSION_NONE) session_start();

$avaliacoesFile = $root . '/data/avaliacoes.json';
$usuariosFile = $root . '/data/usuarios.json';
$midiasFile = $root . '/data/midias.json';

$backup = [];
foreach ([$avaliacoesFile, $usuariosFile, $midiasFile] as $file) {
    $backup[$file] = file_exists($file) ? file_get_contents($file) : null;
}

try {
    file_put_contents($midiasFile, json_encode([['id' => 'midia_1', 'titulo' => 'Mídia Teste', 'tipo' => 'Filme']], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    file_put_contents($usuariosFile, json_encode([['id' => 'user_1', 'nome' => 'Teste User']], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    file_put_contents($avaliacoesFile, json_encode([[ 'id' => 123, 'midia_id' => 'midia_1', 'usuario_id' => 'user_1', 'nota' => 4.5, 'comentario' => 'Ótimo filme', 'data' => '01/01/2026 12:00', 'comentarios' => [] ]], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    $_SESSION['usuario_id'] = 'user_1';
    $_SESSION['usuario_nome'] = 'Teste User';
    $_SERVER['REQUEST_METHOD'] = 'POST';
    $_POST = ['avaliacao_id' => 123, 'comentario' => 'Muito bom'];

    ob_start();
    $controller = new App\Controllers\AvaliacaoController();
    $controller->apiComentar();
    $output = ob_get_clean();
    echo '[[[STATUS]]' . http_response_code() . "\n" . $output;

    $saved = json_decode(file_get_contents($avaliacoesFile), true);
    echo "\n[[[SAVED]]]" . json_encode($saved, JSON_UNESCAPED_UNICODE);
} finally {
    foreach ($backup as $file => $contents) {
        if ($contents === null) {
            @unlink($file);
        } else {
            file_put_contents($file, $contents);
        }
    }
}
