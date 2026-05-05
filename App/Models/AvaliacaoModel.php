<?php

namespace App\Models;

class AvaliacaoModel {
    private $pathAv;
    private $pathMid;
    private $pathUser;

    public function __construct() {
        // Definindo os caminhos de forma robusta
        $this->pathAv = __DIR__ . '/../../data/avaliacoes.json';
        $this->pathMid = __DIR__ . '/../../data/midias.json';
        $this->pathUser = __DIR__ . '/../../data/usuarios.json';

        // Garante que o arquivo de avaliações exista para não dar erro na leitura
        if (!file_exists($this->pathAv)) {
            file_put_contents($this->pathAv, json_encode([], JSON_PRETTY_PRINT));
        }
    }

    /**
     * LÓGICA DE LEITURA: Cruza IDs com nomes reais para mostrar na Home
     */
    public function obterAvaliacoesCompletas() {
        $avaliacoes = $this->carregarJson($this->pathAv);
        $midias = $this->carregarJson($this->pathMid);
        $usuarios = $this->carregarJson($this->pathUser);

        if (empty($avaliacoes)) return [];

        // Cria mapas (dicionários) para busca rápida de títulos e nomes
        $mapaMidias = array_column($midias, 'titulo', 'id');
        $mapaUsuarios = array_column($usuarios, 'nome', 'id');

        foreach ($avaliacoes as &$av) {
            $id_usuario = $av['usuario_id'] ?? '';
            $id_midia = $av['midia_id'] ?? '';
            
            // Adiciona as informações legíveis ao array
            $av['nome_usuario'] = $mapaUsuarios[$id_usuario] ?? 'Usuário Anônimo';
            $av['titulo_midia'] = $mapaMidias[$id_midia] ?? 'Mídia não encontrada';
        }

        return $avaliacoes; 
    }

    /**
     * Retorna as avaliações de um usuário, ordenadas por nota descendente.
     * Se um filtro de nota for passado, mostra apenas avaliações com essa nota.
     */
    public function obterAvaliacoesDoUsuario($usuarioId, $notaFiltro = null) {
        $avaliacoes = $this->obterAvaliacoesCompletas();
        if (empty($avaliacoes)) {
            return [];
        }

        $avaliacoesUsuario = array_filter($avaliacoes, function ($avaliacao) use ($usuarioId, $notaFiltro) {
            if (($avaliacao['usuario_id'] ?? null) != $usuarioId) {
                return false;
            }
            if ($notaFiltro !== null && isset($avaliacao['nota'])) {
                return (int) $avaliacao['nota'] === (int) $notaFiltro;
            }
            return true;
        });

        usort($avaliacoesUsuario, function ($a, $b) {
            return ((int) ($b['nota'] ?? 0)) <=> ((int) ($a['nota'] ?? 0));
        });

        return array_values($avaliacoesUsuario);
    }

    /**
     * LÓGICA DE ESCRITA: Salva a nova avaliação no arquivo JSON
     */
    public function salvar(array $novaAvaliacao): bool {
        $avaliacoes = $this->carregarJson($this->pathAv);
        $avaliacoes[] = $novaAvaliacao; // Adiciona a nova nota no final da lista

        // Grava o array atualizado de volta no arquivo
        return file_put_contents(
            $this->pathAv, 
            json_encode($avaliacoes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        ) !== false;
    }

    /**
     * Função auxiliar para carregar e decodificar arquivos JSON
     */
    private function carregarJson($caminho) {
        if (!file_exists($caminho)) return [];
        $conteudo = file_get_contents($caminho);
        $dados = json_decode($conteudo, true);
        return is_array($dados) ? $dados : [];
    }
    public function obterPorId($id) {
        $avaliacoes = $this->carregarJson($this->pathAv);
        foreach ($avaliacoes as $av) {
            if ($av['id'] == $id) {
                return $av;
            }
        }
        return null;
    }

    public function atualizar($id, $novaNota, $novoComentario) {
        $avaliacoes = $this->carregarJson($this->pathAv);
        $atualizado = false;

        foreach ($avaliacoes as &$av) {
            if ($av['id'] == $id) {
                $av['nota'] = (int) $novaNota;
                $av['comentario'] = $novoComentario;
                $atualizado = true;
                break;
            }
        }

        if ($atualizado) {
            file_put_contents($this->pathAv, json_encode($avaliacoes, JSON_PRETTY_PRINT));
            return true;
        }
        return false;
    }
}