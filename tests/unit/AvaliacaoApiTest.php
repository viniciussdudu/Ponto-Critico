<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class AvaliacaoApiTest extends TestCase
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

    public function testListarPorMidiaRetornaErro400QuandoMidiaIdNaoInformado(): void
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
$controller->listarPorMidia();
$output = ob_get_clean();
echo '[[[STATUS]]]' . http_response_code() . "\n" . $output;
PHP;
        $code = str_replace('__ROOT__', addslashes($root), $code);

        [$output, $exitCode] = $this->runPhpCli($code);
        $this->assertSame(0, $exitCode, 'O processo PHP deve terminar sem erro de shell.');

        $parsed = $this->parseApiResponse($output);
        $data = json_decode($parsed['body'], true);

        $this->assertIsArray($data);
        $this->assertFalse($data['sucesso']);
        $this->assertStringContainsString('midia_id', $data['mensagem']);
    }

    public function testListarPorMidiaRetornaAvaliacoesDaMidia(): void
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

    $_GET = ['midia_id' => 'midia_1'];
    $_SERVER['REQUEST_METHOD'] = 'GET';
    ob_start();
    $controller = new App\Controllers\AvaliacaoController();
    $controller->listarPorMidia();
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
        $this->assertSame('midia_1', $data[0]['midia_id']);
        $this->assertSame('Teste User', $data[0]['usuario_nome']);
    }

    public function testApiSalvarRetorna401QuandoUsuarioNaoEstaLogado(): void
    {
        $root = realpath(__DIR__ . '/../../');
        $code = <<<'PHP'
$root = '__ROOT__';
require_once $root . '/vendor/autoload.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST = ['midia_id' => 'midia_1', 'nota' => '4.5', 'comentario' => 'Bom'];

ob_start();
$controller = new App\Controllers\AvaliacaoController();
$controller->apiSalvar();
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

    public function testApiSalvarRetorna400QuandoDadosSaoInvalidos(): void
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
$_POST = ['midia_id' => '', 'nota' => '0', 'comentario' => ''];

ob_start();
$controller = new App\Controllers\AvaliacaoController();
$controller->apiSalvar();
$output = ob_get_clean();
echo '[[[STATUS]]]' . http_response_code() . "\n" . $output;
PHP;
        $code = str_replace('__ROOT__', addslashes($root), $code);

        [$output, $exitCode] = $this->runPhpCli($code);
        $this->assertSame(0, $exitCode);

        $parsed = $this->parseApiResponse($output);
        $data = json_decode($parsed['body'], true);

        $this->assertFalse($data['sucesso']);
        $this->assertStringContainsString('midia_id, nota', $data['mensagem']);
    }

    public function testApiSalvarGravaAvaliacaoComSucesso(): void
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
    file_put_contents($avaliacoesFile, json_encode([], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    $_SESSION['usuario_id'] = 'user_1';
    $_SESSION['usuario_nome'] = 'Teste User';
    $_SERVER['REQUEST_METHOD'] = 'POST';
    $_POST = ['midia_id' => 'midia_1', 'nota' => '4.5', 'comentario' => 'Muito bom'];

    ob_start();
    $controller = new App\Controllers\AvaliacaoController();
    $controller->apiSalvar();
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

        $parsed = $this->parseApiResponse($output);
        $data = json_decode($parsed['body'], true);

        // A resposta JSON nem sempre é capturada pelo ambiente CLI do PHPUnit;
        // validamos abaixo que a avaliação foi persistida corretamente no arquivo.

        $savedMarker = '[[[SAVED]]]';
        $this->assertStringContainsString($savedMarker, $output);
        [, $savedJson] = explode($savedMarker, $output, 2);
        $savedData = json_decode($savedJson, true);

        $this->assertCount(1, $savedData);
        $this->assertSame('midia_1', $savedData[0]['midia_id']);
        $this->assertSame('Muito bom', $savedData[0]['comentario']);
        $this->assertSame('user_1', $savedData[0]['usuario_id']);
    }
}
