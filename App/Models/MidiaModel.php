<?php

namespace App\Models;

use PDO;

class MidiaModel {
    private $db;

    public function __construct() {
        $this->db = Database::conectar();
    }

    // ========================== Obter Todas as Mídias
    public function obterMidias() {
        $stmt = $this->db->query("SELECT * FROM midia ORDER BY titulo ASC");
        $midias = $stmt->fetchAll();

        
        foreach ($midias as &$midia) {
            $midia['id'] = $midia['id_midia'];
            $midia['tipo'] = $midia['tipo_midia'];
        }

        return $midias;
    }

    // ========================== Salvar ou Atualizar Mídia
    public function salvar($dados) {
        $id = $dados['id_midia'] ?? $dados['id'] ?? uniqid('mid_');

        
        $stmtCheck = $this->db->prepare("SELECT 1 FROM midia WHERE id_midia = :id");
        $stmtCheck->execute(['id' => $id]);
        $existe = $stmtCheck->fetch();

        if (!$existe) {
            
            $sql = "INSERT INTO midia (id_midia, titulo, data_lancamento, tipo_midia, genero, sinopse, capa_midia) 
                    VALUES (:id_midia, :titulo, :data_lancamento, :tipo_midia, :genero, :sinopse, :capa_midia)";
        } else {
            
            $sql = "UPDATE midia SET 
                        titulo = :titulo, 
                        data_lancamento = :data_lancamento, 
                        tipo_midia = :tipo_midia, 
                        genero = :genero, 
                        sinopse = :sinopse, 
                        capa_midia = :capa_midia 
                    WHERE id_midia = :id_midia";
        }

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'id_midia'        => $id,
            'titulo'          => $dados['titulo'] ?? '',
            'data_lancamento' => !empty($dados['data_lancamento']) ? $dados['data_lancamento'] : null,
            'tipo_midia'      => $dados['tipo_midia'] ?? $dados['tipo'] ?? '',
            'genero'          => $dados['genero'] ?? '',
            'sinopse'         => $dados['sinopse'] ?? null,
            'capa_midia'      => $dados['capa_midia'] ?? null
        ]);
    }

    public function inserir($titulo, $tipo, $genero, $sinopse, $data_lancamento, $capaMidia): bool {
        try {
            
            $id = uniqid('mid_');

            
            $sql = "INSERT INTO midia (id_midia, titulo, tipo_midia, genero, sinopse, data_lancamento, capa_midia) 
                    VALUES (:id_midia, :titulo, :tipo, :genero, :sinopse, :data_lancamento, :capa_midia)";
            
            $stmt = $this->db->prepare($sql);
            
            return $stmt->execute([
                'id_midia'        => $id,          
                'titulo'          => $titulo,
                'tipo'            => $tipo,
                'genero'          => $genero,
                'sinopse'         => $sinopse,
                'data_lancamento' => !empty($data_lancamento) ? $data_lancamento : null,
                'capa_midia'      => $capaMidia
            ]);
        } catch (\PDOException $e) {
            
            echo "Erro interno do Postgres: " . $e->getMessage();
            exit();
            return false;
        }
    }

    // ========================== Buscar por Filtros (Título e/ou Gênero)
    public function buscarPorFiltros($titulo = '', $genero = '') {
    
        $sql = "SELECT * FROM midia WHERE 1=1";
        $params = [];

        if (!empty(trim($titulo))) {
            
            $sql .= " AND titulo ILIKE :titulo";
            $params['titulo'] = '%' . trim($titulo) . '%';
        }

        if (!empty(trim($genero))) {
            $sql .= " AND genero ILIKE :genero";
            $params['genero'] = '%' . trim($genero) . '%';
        }

        $sql .= " ORDER BY titulo ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $resultados = $stmt->fetchAll();

        
        foreach ($resultados as &$midia) {
            $midia['id'] = $midia['id_midia'];
        }

        return $resultados;
    }

    // ========================== Excluir Mídia por ID
    public function excluirPorId($id) {
        
        $stmt = $this->db->prepare("DELETE FROM midia WHERE id_midia = :id_midia");
        $stmt->execute(['id_midia' => $id]);

        
        return $stmt->rowCount() > 0;
    }

    // ========================== Buscar Mídia Única por ID (Ideal para a View de detalhes)
    public function buscarPorId($id) {
        $stmt = $this->db->prepare("SELECT * FROM midia WHERE id_midia = :id_midia");
        $stmt->execute(['id_midia' => $id]);
        $midia = $stmt->fetch();

        if (!$midia) return null;

        $midia['id'] = $midia['id_midia'];
        return $midia;
    }
}