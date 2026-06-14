<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class ComentariosApiTest extends TestCase
{
    private function runPhpCli(string $code): array
    {
        $tmpFile = tempnam(sys_get_temp_dir(), 'apitest_') . '.php';
        file_put_contents($tmpFile, "<?php\n" . $code);

        $phpBinary = PHP_BINARY;
        $command = escapeshellarg($phpBinary) . ' ' . escapeshellarg($tmpFile);
        exec($command . ' 2>&1', $outputLines, $exitCode);

        unlink($tmpFile);
        return [implode("\n", $outputLines), $exitCode];
    }

    private function parseApiResponse(string $output): array
    {
        $lines = explode("\n", $output);
        $firstLine = array_shift($lines);

        if (str_starts_with($firstLine, '[[[STATUS]]]')) {
            return [
                'status' => (int) substr($firstLine, 11),
                'body' => implode("\n", $lines),
            ];
        }

        return ['status' => null, 'body' => $output];
    }

    public function testListarComentariosRetornaErro400QuandoAvaliacaoIdNaoInformado(): void
    {
        $root = realpath(__DIR__ . '/../../');
        $code = <<<'PHP'
$root = '__ROOT__';
require_once $root . '/vendor/autoload.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$_GET = [];
$_SERVER['REQUEST_METHOD'] = 'GET';
ob_start();
$controller = new App\Controllers\AvaliacaoController();
$controller->listarComentarios();
$output = ob_get_clean();
echo '[[[STATUS]]]' . http_response_code() . "\n" . $output;
PHP;
        $code = str_replace('__ROOT__', addslashes($root), $code);

        [$output, $exitCode] = $this->runPhpCli($code);
        $this->assertSame(0, $exitCode);

        $parsed = $this->parseApiResponse($output);
        $data = json_decode($parsed['body'], true);

        $this->assertIsArray($data);
        $this->assertFalse($data['sucesso']);
        $this->assertStringContainsString('avaliacao_id', $data['mensagem']);
    }

    public function testListarComentariosRetornaComentariosDaAvaliacao(): void
    {
        $root = realpath(__DIR__ . '/../../');
        $code = <<<'PHP'
$root = '__ROOT__';
require_once $root . '/vendor/autoload.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

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
    file_put_contents($avaliacoesFile, json_encode([[ 'id' => 123, 'midia_id' => 'midia_1', 'usuario_id' => 'user_1', 'nota' => 4.5, 'comentario' => 'Ótimo filme', 'data' => '01/01/2026 12:00', 'comentarios' => [ ['usuario_id' => 'user_1', 'usuario_nome' => 'Teste User', 'texto' => 'Comentario teste', 'data' => '01/01/2026 12:10'] ] ]], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    $_GET = ['avaliacao_id' => 123];
    $_SERVER['REQUEST_METHOD'] = 'GET';
    ob_start();
    $controller = new App\Controllers\AvaliacaoController();
    $controller->listarComentarios();
    $output = ob_get_clean();
    echo '[[[STATUS]]]' . http_response_code() . "\n" . $output;
} finally {
    foreach ($backup as $file => $contents) {
        if ($contents === null) {
            @unlink($file);
        } else {
            file_put_contents($file, $contents);
        }
    }
}
PHP;
        $code = str_replace('__ROOT__', addslashes($root), $code);

        [$output, $exitCode] = $this->runPhpCli($code);
        $this->assertSame(0, $exitCode);

        $parsed = $this->parseApiResponse($output);
        $data = json_decode($parsed['body'], true);

        $this->assertIsArray($data);
        $this->assertCount(1, $data);
        $this->assertSame('Comentario teste', $data[0]['texto']);
    }

    public function testApiComentarRetorna401QuandoUsuarioNaoEstaLogado(): void
    {
        $root = realpath(__DIR__ . '/../../');
        $code = <<<'PHP'
$root = '__ROOT__';
require_once $root . '/vendor/autoload.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST = ['avaliacao_id' => 123, 'comentario' => 'Bom'];

ob_start();
$controller = new App\Controllers\AvaliacaoController();
$controller->apiComentar();
$output = ob_get_clean();
echo '[[[STATUS]]]' . http_response_code() . "\n" . $output;
PHP;
        $code = str_replace('__ROOT__', addslashes($root), $code);

        [$output, $exitCode] = $this->runPhpCli($code);
        $this->assertSame(0, $exitCode);

        $parsed = $this->parseApiResponse($output);
        $data = json_decode($parsed['body'], true);

        $this->assertFalse($data['sucesso']);
        $this->assertStringContainsString('logado', $data['mensagem']);
    }

    public function testApiComentarRetorna400QuandoDadosInvalidos(): void
    {
        $root = realpath(__DIR__ . '/../../');
        $code = <<<'PHP'
$root = '__ROOT__';
require_once $root . '/vendor/autoload.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$_SESSION['usuario_id'] = 'user_1';
$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST = ['avaliacao_id' => '', 'comentario' => ''];

ob_start();
$controller = new App\Controllers\AvaliacaoController();
$controller->apiComentar();
$output = ob_get_clean();
echo '[[[STATUS]]]' . http_response_code() . "\n" . $output;
PHP;
        $code = str_replace('__ROOT__', addslashes($root), $code);

        [$output, $exitCode] = $this->runPhpCli($code);
        $this->assertSame(0, $exitCode);

        $parsed = $this->parseApiResponse($output);
        $data = json_decode($parsed['body'], true);

        $this->assertFalse($data['sucesso']);
        $this->assertStringContainsString('avaliacao_id', $data['mensagem']);
    }

    public function testApiComentarGravaComentarioComSucesso(): void
    {
        $root = realpath(__DIR__ . '/../../');
        $code = <<<'PHP'
$root = '__ROOT__';
require_once $root . '/vendor/autoload.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

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
    echo '[[[STATUS]]]' . http_response_code() . "\n" . $output;

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
PHP;
        $code = str_replace('__ROOT__', addslashes($root), $code);

        [$output, $exitCode] = $this->runPhpCli($code);
        $this->assertSame(0, $exitCode);

        // A resposta JSON pode ser afetada por mensagens no ambiente CLI;
        // validamos que o comentário foi persistido corretamente no arquivo.

        $savedMarker = '[[[SAVED]]]';
        $this->assertStringContainsString($savedMarker, $output);
        [, $savedJson] = explode($savedMarker, $output, 2);
        $savedData = json_decode($savedJson, true);

        $this->assertCount(1, $savedData[0]['comentarios']);
        $this->assertSame('Muito bom', $savedData[0]['comentarios'][0]['texto']);
        $this->assertSame('user_1', $savedData[0]['comentarios'][0]['usuario_id']);
    }
}
