<?php

namespace App\Models;

class ListaModel {
    private $arquivolistas = __DIR__ . '/../../data/listas.json';
    private $arquivoVinculos = __DIR__ . '/../../data/lista_midias.json';

    // Função auxiliar para ler um arquivo JSON com segurança
    private function lerJson($caminho) {
        if (!file_exists($caminho)) {
            return [];
        }
        $conteudo = file_get_contents($caminho);
        return json_decode($conteudo, true) ?: [];
    }

    // Função auxiliar para salvar os dados de volta no arquivo JSON
    private function salvarJson($caminho, $dados) {
        // O argumento JSON_PRETTY_PRINT deixa o arquivo legível para você testar
        file_put_contents($caminho, json_encode($dados, JSON_PRETTY_PRINT));
    }

    // 1. Busca as listas criadas por um usuário específico
    public function buscarListasPorUsuario($usuarioId) {
        $listas = $this->lerJson($this->arquivolistas);
        
        // Filtra o array mantendo apenas as listas do usuário logado
        return array_filter($listas, function($lista) use ($usuarioId) {
            return $lista['usuario_id'] == $usuarioId;
        });
    }

    // 2. Cria uma nova lista gerando um ID incremental automático
    public function criarNovaLista($nome, $usuarioId) {
        $listas = $this->lerJson($this->arquivolistas);

        // Gera um ID incremental simples baseado no maior ID existente
        $novoId = empty($listas) ? 1 : max(array_column($listas, 'id')) + 1;

        $novaLista = [
            'id' => $novoId,
            'nome' => $nome,
            'usuario_id' => $usuarioId,
            'data_criacao' => date('Y-m-d H:i:s')
        ];

        $listas[] = $novaLista;
        $this->salvarJson($this->arquivolistas, $listas);
        return $novoId;
    }

    // 3. Busca os dados de uma única lista pelo ID dela
    public function buscarPorId($listaId) {
        $listas = $this->lerJson($this->arquivolistas);
        foreach ($listas as $lista) {
            if ($lista['id'] == $listaId) {
                return $lista;
            }
        }
        return null;
    }

    // 4. Busca as mídias associadas a uma lista (Faz o papel do "JOIN")
    public function buscarMidiasDaLista($listaId) {
        $vinculos = $this->lerJson($this->arquivoVinculos);
        
        $midiaModel = new MidiaModel();
        $todasAsMidias = $midiaModel->obterMidias(); //  MUDADO para obterMidias()

        $midiasFiltradas = [];

        if (is_array($vinculos) && is_array($todasAsMidias)) {
            foreach ($vinculos as $vinculo) {
                if ($vinculo['lista_id'] == $listaId) {
                    foreach ($todasAsMidias as $midia) {
                        if ($midia['id'] == $vinculo['midia_id']) {
                            $midiasFiltradas[] = $midia;
                        }
                    }
                }
            }
        }
        return $midiasFiltradas;
    }

    // 5. Verifica se a mídia já foi adicionada para evitar duplicados
    public function midiaJaExisteNaLista($listaId, $midiaId) {
        $vinculos = $this->lerJson($this->arquivoVinculos);
        foreach ($vinculos as $vinculo) {
            if ($vinculo['lista_id'] == $listaId && $vinculo['midia_id'] == $midiaId) {
                return true;
            }
        }
        return false;
    }

    // 6. Vincula a mídia inserindo o registro no JSON pivô
    public function adicionarMidiaNaLista($listaId, $midiaId) {
        $vinculos = $this->lerJson($this->arquivoVinculos);

        $vinculos[] = [
            'lista_id' => (int)$listaId,
            'midia_id' => $midiaId
        ];

        $this->salvarJson($this->arquivoVinculos, $vinculos);
    }
}