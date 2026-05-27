<?php
$code = <<<'PHP'
require_once __DIR__ . '/vendor/autoload.php';
if (session_status() === PHP_SESSION_NONE) session_start();

$avaliacoesFile = __DIR__ . '/data/avaliacoes.json';
$usuariosFile = __DIR__ . '/data/usuarios.json';
$midiasFile = __DIR__ . '/data/midias.json';

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
echo '[[[STATUS]]]' . http_response_code() . "\n" . $output;
PHP;

$tmp = tempnam(sys_get_temp_dir(), 'sim_') . '.php';
file_put_contents($tmp, "<?php\n" . $code);
$php = PHP_BINARY;
$cmd = escapeshellarg($php) . ' ' . escapeshellarg($tmp) . ' 2>&1';
exec($cmd, $outLines, $exit);
echo "EXIT=$exit\n";
echo "OUTPUT=\n" . implode("\n", $outLines) . "\n";
@unlink($tmp);
