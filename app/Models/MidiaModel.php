<?php
namespace App\Models;

class MidiaModel {
    private $caminhoArquivo;

    public function __construct() {
        $this->caminhoArquivo = __DIR__ . '/../../data/midias.json';
    }

    public function obterMidias() {
        $conteudo = file_get_contents($this->caminhoArquivo);
        return json_decode($conteudo, true);
    }

    public function atualizarMidias($dadosNovos) {
        $novoJson = json_encode($dadosNovos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        return file_put_contents($this->caminhoArquivo, $novoJson);
    }
}