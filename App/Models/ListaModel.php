<?php

namespace App\Models;

use PDO;

class ListaModel {
    private $db;

    public function __construct() {
        $this->db = Database::conectar();
    }

    // 1. Busca as listas criadas por um usuário específico
    public function buscarListasPorUsuario($usuarioId) {
        $stmt = $this->db->prepare("SELECT id_lista AS id, nome_lista AS nome, id_usuario AS usuario_id, data_criacao FROM listas WHERE id_usuario = :id_usuario ORDER BY data_criacao DESC");
        $stmt->execute(['id_usuario' => $usuarioId]);
        return $stmt->fetchAll();
    }

    // 2. Cria uma nova lista usando o SERIAL (auto_increment) do Postgres
    public function criarNovaLista($nome, $usuarioId) {
        $sql = "INSERT INTO listas (nome_lista, id_usuario) VALUES (:nome_lista, :id_usuario) RETURNING id_lista";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'nome_lista' => $nome,
            'id_usuario' => $usuarioId
        ]);
        
        return $stmt->fetchColumn();
    }

    // 3. Busca os dados de uma única lista pelo ID dela
    public function buscarPorId($listaId) {
        $stmt = $this->db->prepare("SELECT id_lista AS id, nome_lista AS nome, id_usuario AS usuario_id, data_criacao FROM listas WHERE id_lista = :id_lista");
        $stmt->execute(['id_lista' => $listaId]);
        $lista = $stmt->fetch();

        return $lista ?: null;
    }

    // 4. Busca as mídias associadas a uma lista (Faz o papel real do "JOIN")
    public function buscarMidiasDaLista($listaId) {
        $sql = "SELECT m.id_midia AS id, m.titulo, m.data_lancamento, m.tipo_midia, m.genero, m.sinopse, m.capa_midia, ml.posicao
                FROM midia m
                INNER JOIN midias_lista ml ON m.id_midia = ml.id_midia
                WHERE ml.id_lista = :id_lista
                ORDER BY ml.posicao ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id_lista' => $listaId]);
        return $stmt->fetchAll();
    }

    // 5. Verifica se a mídia já foi adicionada para evitar duplicados
    public function midiaJaExisteNaLista($listaId, $midiaId) {
        $stmt = $this->db->prepare("SELECT 1 FROM midias_lista WHERE id_lista = :id_lista AND id_midia = :id_midia");
        $stmt->execute([
            'id_lista' => $listaId,
            'id_midia' => $midiaId
        ]);
        return (bool)$stmt->fetch();
    }

    // 6. Vincula a mídia inserindo o registro na tabela pivot
    public function adicionarMidiaNaLista($listaId, $midiaId) {
        $stmtPos = $this->db->prepare("SELECT COALESCE(MAX(posicao), 0) + 1 FROM midias_lista WHERE id_lista = :id_lista");
        $stmtPos->execute(['id_lista' => $listaId]);
        $proximaPosicao = $stmtPos->fetchColumn();

        $sql = "INSERT INTO midias_lista (id_lista, id_midia, posicao) VALUES (:id_lista, :id_midia, :posicao)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'id_lista' => (int)$listaId,
            'id_midia' => $midiaId,
            'posicao'  => $proximaPosicao
        ]);
    }

    public function deletarListaPorId($listaId, $usuarioId): bool {
        try {
            $this->db->beginTransaction();

            $stmtVinculo = $this->db->prepare("DELETE FROM midias_lista WHERE id_lista = :lista_id");
            $stmtVinculo->execute(['lista_id' => $listaId]);

            
            $stmtLista = $this->db->prepare("DELETE FROM listas WHERE id_lista = :lista_id AND id_usuario = :usuario_id");
            $stmtLista->execute([
                'lista_id' => $listaId,
                'usuario_id' => $usuarioId
            ]);

            
            $this->db->commit();
            return true;

        } catch (\PDOException $e) {
            
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            return false;
        }
    }
}