<?php

namespace App\Models;

use PDO;

class AvaliacaoModel {
    private $db;

    public function __construct() {
       
        $this->db = Database::conectar();
    }

    
    public function obterAvaliacoesCompletas() {
        $sql = "SELECT 
                    a.id_avaliacao AS id,
                    a.comentario,
                    a.nota,
                    a.data_avaliacao,
                    a.id_midia AS midia_id,
                    a.id_usuario AS usuario_id,
                    u.nome AS usuario_nome,
                    m.titulo AS titulo_midia
                FROM avaliacao a
                INNER JOIN usuario u ON a.id_usuario = u.id_usuario
                INNER JOIN midia m ON a.id_midia = m.id_midia
                ORDER BY a.data_avaliacao DESC";

        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    /**
     * Retorna as avaliações de um usuário filtradas ou não por nota
     */
    public function obterAvaliacoesDoUsuario($usuarioId, $notaFiltro = null) {
        $sql = "SELECT 
                    a.id_avaliacao AS id, a.comentario, a.nota, a.data_avaliacao,
                    a.id_midia AS midia_id, a.id_usuario AS usuario_id,
                    u.nome AS usuario_nome, m.titulo AS titulo_midia
                FROM avaliacao a
                INNER JOIN usuario u ON a.id_usuario = u.id_usuario
                INNER JOIN midia m ON a.id_midia = m.id_midia
                WHERE a.id_usuario = :usuario_id";
        
        $params = ['usuario_id' => $usuarioId];

        if ($notaFiltro !== null) {
            $sql .= " AND a.nota = :nota";
            $params['nota'] = (int)$notaFiltro;
        }

        $sql .= " ORDER BY a.nota DESC, a.data_avaliacao DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Retorna todas as avaliações de uma mídia específica
     */
    public function obterAvaliacoesDaMidia($midiaId) {
        $sql = "SELECT 
                    a.id_avaliacao AS id, a.comentario, a.nota, a.data_avaliacao,
                    a.id_midia AS midia_id, a.id_usuario AS usuario_id,
                    u.nome AS usuario_nome, m.titulo AS titulo_midia
                FROM avaliacao a
                INNER JOIN usuario u ON a.id_usuario = u.id_usuario
                INNER JOIN midia m ON a.id_midia = m.id_midia
                WHERE a.id_midia = :midia_id
                ORDER BY a.nota DESC, a.data_avaliacao DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['midia_id' => $midiaId]);
        return $stmt->fetchAll();
    }

    /**
     * Calcula a nota média de uma mídia usando a função nativa AVG do SQL
     */
    public function calcularNotaMedia($midiaId) {
        $stmt = $this->db->prepare("SELECT AVG(nota) as media FROM avaliacao WHERE id_midia = :midia_id");
        $stmt->execute(['midia_id' => $midiaId]);
        $resultado = $stmt->fetch();
        
        return $resultado['media'] ? round($resultado['media'], 2) : 0;
    }

    /**
     * LÓGICA DE ESCRITA: Insere ou atualiza uma avaliação
     */
    public function salvar(array $dados): bool {
        $sql = "INSERT INTO avaliacao (comentario, nota, id_midia, id_usuario) 
                VALUES (:comentario, :nota, :id_midia, :id_usuario)";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'comentario' => $dados['comentario'] ?? null,
            'nota'       => (int)($dados['nota'] ?? 0),
            'id_midia'   => $dados['midia_id'] ?? $dados['id_midia'] ?? '',
            'id_usuario' => (int)($dados['usuario_id'] ?? $dados['id_usuario'] ?? 0)
        ]);
    }

    public function obterPorId($id) {
        // 1. Busca os dados básicos da avaliação (sua query atual)
        $sql = "SELECT 
                    a.id_avaliacao AS id, a.comentario, a.nota, a.data_avaliacao AS data,
                    a.id_midia AS midia_id, a.id_usuario AS usuario_id,
                    u.nome AS nome_usuario, m.titulo AS titulo_midia
                FROM avaliacao a
                INNER JOIN usuario u ON a.id_usuario = u.id_usuario
                INNER JOIN midia m ON a.id_midia = m.id_midia
                WHERE a.id_avaliacao = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        $avaliacao = $stmt->fetch();

        // Se não encontrar a avaliação, retorna null
        if (!$avaliacao) {
            return null;
        }

        // 2. BUSCA OS VOTOS DESSA AVALIAÇÃO PARA ALIMENTAR A VIEW
        $sqlVotos = "SELECT id_usuario, curtida FROM votos_avaliacao WHERE id_avaliacao = :id_avaliacao";
        $stmtVotos = $this->db->prepare($sqlVotos);
        $stmtVotos->execute(['id_avaliacao' => $id]);
        $votos = $stmtVotos->fetchAll();

        // Inicializa os arrays que a View está esperando lá no topo do arquivo
        $avaliacao['likes'] = [];
        $avaliacao['deslikes'] = [];

        // Separa quem deu 'like' e quem deu 'dislike'
        foreach ($votos as $voto) {
            if ($voto['curtida'] === 'like') {
                $avaliacao['likes'][] = $voto['id_usuario'];
            } elseif ($voto['curtida'] === 'dislike') {
                $avaliacao['deslikes'][] = $voto['id_usuario']; 
            }
        }

        // 3. BUSCA OS COMENTÁRIOS DA AVALIAÇÃO (Garante que a área de respostas continue funcionando)
        $sqlComentarios = "SELECT 
                                c.conteudo AS texto, 
                                c.data_criacao AS data, 
                                u.nome AS usuario_nome 
                           FROM comentario c
                           INNER JOIN usuario u ON c.id_usuario = u.id_usuario
                           WHERE c.id_avaliacao = :id_avaliacao
                           ORDER BY c.data_criacao ASC";
        
        $stmtComent = $this->db->prepare($sqlComentarios);
        $stmtComent->execute(['id_avaliacao' => $id]);
        $avaliacao['comentarios'] = $stmtComent->fetchAll();

        return $avaliacao;
    }

    public function atualizar($id, $novaNota, $novoComentario) {
        $sql = "UPDATE avaliacao SET nota = :nota, comentario = :comentario WHERE id_avaliacao = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'nota'       => (int)$novaNota,
            'comentario' => $novoComentario,
            'id'         => $id
        ]);
    }

    /**
     * GERENCIAMENTO DE VOTOS (LIKE / DISLIKE) USANDO O TIPO ENUM DO POSTGRES
     */
    public function adicionarLikeAvaliacao($id_avaliacao, $usuarioId): bool {
        return $this->processarVotoAvaliacao($usuarioId, $id_avaliacao, 'like');
    }

    public function adicionarDeslikeAvaliacao($id_avaliacao, $usuarioId): bool {
        return $this->processarVotoAvaliacao($usuarioId, $id_avaliacao, 'dislike');
    }

    private function processarVotoAvaliacao($usuarioId, $id_avaliacao, $tipoVoto): bool {
        try {
            $usuarioId = (int)$usuarioId;
            $id_avaliacao = (int)$id_avaliacao;

            // 1. Verifica se o voto composto já existe na tabela correta
            $stmt = $this->db->prepare("SELECT curtida FROM votos_avaliacao WHERE id_usuario = :id_usuario AND id_avaliacao = :id_avaliacao");
            $stmt->execute(['id_usuario' => $usuarioId, 'id_avaliacao' => $id_avaliacao]);
            $votoExistente = $stmt->fetch();

            if ($votoExistente) {
                if ($votoExistente['curtida'] === $tipoVoto) {
                    $sql = "UPDATE votos_avaliacao SET curtida = NULL WHERE id_usuario = :id_usuario AND id_avaliacao = :id_avaliacao";
                    $params = ['id_usuario' => $usuarioId, 'id_avaliacao' => $id_avaliacao];
                } else {
                    $sql = "UPDATE votos_avaliacao SET curtida = :tipo_voto WHERE id_usuario = :id_usuario AND id_avaliacao = :id_avaliacao";
                    $params = [
                        'id_usuario' => $usuarioId, 
                        'id_avaliacao' => $id_avaliacao, 
                        'tipo_voto' => $tipoVoto
                    ];
                }
                $stmtUpdate = $this->db->prepare($sql);
                return $stmtUpdate->execute($params);
            } else {
                $sql = "INSERT INTO votos_avaliacao (id_usuario, id_avaliacao, curtida) VALUES (:id_usuario, :id_avaliacao, :tipo_voto)";
                $stmtInsert = $this->db->prepare($sql);
                return $stmtInsert->execute([
                    'id_usuario' => $usuarioId,
                    'id_avaliacao' => $id_avaliacao,
                    'tipo_voto' => $tipoVoto
                ]);
            }
        } catch (\PDOException $e) {
            return false;
        }
    }
    /**
     * COMENTÁRIOS: Insere uma resposta/comentário vinculado a uma avaliação
     */
    public function adicionarComentario($id_avaliacao, array $comentario): bool {
        $sql = "INSERT INTO comentario (conteudo, id_avaliacao, id_usuario) 
                VALUES (:conteudo, :id_avaliacao, :id_usuario)";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'conteudo'     => $comentario['conteudo'] ?? $comentario['texto'] ?? '',
            'id_avaliacao' => (int)$id_avaliacao,
            'id_usuario'   => (int)($comentario['usuario_id'] ?? 0)
        ]);
    }
    // ====================================== Excluir Avaliação por ID
    public function excluirAvaliacaoPorId($idAvaliacao, $idUsuario, $usuarioTipo): bool {
        try {
            // 1. Busca a avaliação para verificar quem é o dono real dela antes de apagar
            $stmtCheck = $this->db->prepare("SELECT id_usuario FROM avaliacao WHERE id_avaliacao = :id");
            $stmtCheck->execute(['id' => $idAvaliacao]);
            $avaliacao = $stmtCheck->fetch();

            if (!$avaliacao) {
                return false; 
            }

            // 2. Trava de segurança no Banco: Só apaga se for o dono ou se for um administrador
            if ($avaliacao['id_usuario'] != $idUsuario && $usuarioTipo !== 'admin') {
                return false; 
            }

            // 3. Executa a exclusão física no PostgreSQL
            $stmtDelete = $this->db->prepare("DELETE FROM avaliacao WHERE id_avaliacao = :id");
            return $stmtDelete->execute(['id' => $idAvaliacao]);

        } catch (\PDOException $e) {
            return false;
        }
    }
}