<?php

namespace App\Models;

class Avaliacao
{
    private string $arquivo;

    public function __construct()
    {
        $this->arquivo = __DIR__ . '/../../data/avaliacoes.json';

        if (!file_exists($this->arquivo)) {
            file_put_contents($this->arquivo, json_encode([], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        }
    }

    public function salvar(array $avaliacao): bool
    {
        $avaliacoes = $this->listarTodas();
        $avaliacoes[] = $avaliacao;

        return file_put_contents(
            $this->arquivo,
            json_encode($avaliacoes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        ) !== false;
    }

    public function listarTodas(): array
    {
        if (!file_exists($this->arquivo)) {
            return [];
        }

        $conteudo = file_get_contents($this->arquivo);
        $dados = json_decode($conteudo, true);

        return is_array($dados) ? $dados : [];
    }

    public function listarPorMidiaId(int $midiaId): array
    {
        $avaliacoes = $this->listarTodas();

        return array_values(array_filter($avaliacoes, function ($avaliacao) use ($midiaId) {
            return isset($avaliacao['midia_id']) && (int)$avaliacao['midia_id'] === $midiaId;
        }));
    }
}