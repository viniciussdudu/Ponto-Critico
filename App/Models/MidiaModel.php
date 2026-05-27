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
    public function buscarPorTitulo($titulo = '') {
        $midias = $this->obterMidias();

        if (!is_array($midias)) {
            return [];
        }

        // Se nenhum título for enviado, retorna todas as mídias
        if (empty(trim($titulo))) {
            return $midias;
        }

        $tituloFiltrado = mb_strtolower(trim($titulo), 'UTF-8');

        // Filtra o array buscando ocorrências do termo no título da mídia
        $resultados = array_filter($midias, function($midia) use ($tituloFiltrado) {
            $tituloMidia = mb_strtolower($midia['titulo'] ?? '', 'UTF-8');
            return mb_strpos($tituloMidia, $tituloFiltrado) !== false;
        });

        // array_values garante que os índices numéricos do JSON fiquem sequenciais ([0, 1, 2...])
        return array_values($resultados);
    }

    public function excluirPorId($id) {
    $midias = $this->obterMidias();

    if (!is_array($midias)) {
        return false;
    }

    $quantidadeAntes = count($midias);

    $midiasFiltradas = array_filter($midias, function($midia) use ($id) {
        return ($midia['id'] ?? '') !== $id;
    });

    $midiasFiltradas = array_values($midiasFiltradas);

    if (count($midiasFiltradas) === $quantidadeAntes) {
        return false;
    }

    return $this->atualizarMidias($midiasFiltradas);
}
}
