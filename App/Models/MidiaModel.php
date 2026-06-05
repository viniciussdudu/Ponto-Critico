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
    public function buscarPorFiltros($titulo = '', $genero = '') {
        $midias = $this->obterMidias();

        if (!is_array($midias)) {
            return [];
        }

        $tituloFiltrado = mb_strtolower(trim($titulo), 'UTF-8');
        $generoFiltrado = mb_strtolower(trim($genero), 'UTF-8');

        // Filtra o array buscando ocorrências de ambos os termos
        $resultados = array_filter($midias, function($midia) use ($tituloFiltrado, $generoFiltrado) {
            $tituloMidia = mb_strtolower($midia['titulo'] ?? '', 'UTF-8');
            $generoMidia = mb_strtolower($midia['genero'] ?? '', 'UTF-8');

            // Verifica se o título bate (se foi informado)
            $bateTitulo = empty($tituloFiltrado) || (mb_strpos($tituloMidia, $tituloFiltrado) !== false);

            // Verifica se o gênero bate (se foi informado)
            $bateGenero = empty($generoFiltrado) || (mb_strpos($generoMidia, $generoFiltrado) !== false);

            // A mídia precisa atender a ambos os filtros ativos
            return $bateTitulo && $bateGenero;
        });

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
