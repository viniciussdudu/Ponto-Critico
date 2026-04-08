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

    private function carregarJson($caminho) {
        if (!file_exists($caminho)) {
            return [];
        }
        
        $conteudo = file_get_contents($caminho);
        $dados = json_decode($conteudo, true);
        
        return is_array($dados) ? $dados : [];
    }
}