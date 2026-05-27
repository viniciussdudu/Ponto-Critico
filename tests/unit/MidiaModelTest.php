<?php
require_once __DIR__ . '/../../vendor/autoload.php';

// Carregador de classes específico do projeto Ponto Crítico
spl_autoload_register(function ($class) {
    $path = __DIR__ . '/../../' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($path)) {
        require_once $path;
    }
});

use PHPUnit\Framework\TestCase;
use App\Models\MidiaModel;

class MidiaModelTest extends TestCase {

    public function testBuscarPorTituloRetornaMidiaCorreta() {
        $model = new MidiaModel();

        $resultado = $model->buscarPorTitulo('Teste');

        $this->assertNotEmpty($resultado);
        $this->assertEquals('Mídia Teste', $resultado[0]['titulo']);
    }

    public function testBuscarPorTituloInexistenteRetornaVazio() {
        $model = new MidiaModel();

        $resultado = $model->buscarPorTitulo('Vingadores');

        $this->assertEmpty($resultado);
    }
}
