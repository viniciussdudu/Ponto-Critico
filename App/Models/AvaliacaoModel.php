<?php
namespace App\Models;

class AvaliacaoModel {
    private $pathAv = __DIR__ . '/../../data/avaliacoes.json';
    private $pathMid = __DIR__ . '/../../data/midias.json';
    private $pathUser = __DIR__ . '/../../data/usuarios.json';

    public function obterAvaliacoesCompletas() {
        $avaliacoes = $this->carregarJson($this->pathAv);
        $midias = $this->carregarJson($this->pathMid);
        $usuarios = $this->carregarJson($this->pathUser);

        if (!is_array($avaliacoes) || empty($avaliacoes)) {
            return [];
        }

        $mapaMidias = is_array($midias) ? array_column($midias, 'titulo', 'id') : [];
        $mapaUsuarios = is_array($usuarios) ? array_column($usuarios, 'nome', 'id') : [];

        foreach ($avaliacoes as &$av) {
            $id_usuario = $av['usuario_id'] ?? '';
            $id_midia = $av['midia_id'] ?? '';
            
            $av['nome_usuario'] = $mapaUsuarios[$id_usuario] ?? 'Usuário Anônimo';
            $av['titulo_midia'] = $mapaMidias[$id_midia] ?? 'Mídia não encontrada';
        }

        return $avaliacoes; 
    }
    // Busca apenas uma avaliação específica pelo ID
    public function obterPorId($id) {
        $avaliacoes = $this->carregarJson($this->pathAv);
        foreach ($avaliacoes as $av) {
            if ($av['id'] === $id) {
                return $av;
            }
        }
        return null;
    }

    // Salva a nova nota e o novo comentário no JSON
    public function atualizar($id, $novaNota, $novoComentario) {
        $avaliacoes = $this->carregarJson($this->pathAv);
        $atualizado = false;

        foreach ($avaliacoes as &$av) {
            if ($av['id'] === $id) {
                $av['nota'] = (int) $novaNota;
                $av['comentario'] = $novoComentario;
                $atualizado = true;
                break; // Para o loop pois já achou o que queria
            }
        }

        if ($atualizado) {
            // Salva de volta no arquivo json com a formatação bonita
            file_put_contents($this->pathAv, json_encode($avaliacoes, JSON_PRETTY_PRINT));
            return true;
        }
        return false;
    }

    private function carregarJson($caminho) {
        if (!file_exists($caminho)) {
            return [];
        }
        
        $conteudo = file_get_contents($caminho);
        $dados = json_decode($conteudo, true);
        
        return is_array($dados) ? $dados : [];
    }
}