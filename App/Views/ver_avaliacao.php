<div class="page detalhe-page">
    <section class="card card-detalhe-avaliacao">
        <a href="index.php?url=home" class="link-voltar">← Voltar para Home</a>

        <h1><?= htmlspecialchars($avaliacao['titulo_midia'] ?? 'Avaliação') ?></h1>

        <p class="subtitulo-home">
            Avaliação feita por <?= htmlspecialchars($avaliacao['nome_usuario'] ?? 'Usuário') ?>
        </p>

        <div class="avaliacao-detalhe-box">
            <p class="estrelas">
                <?= str_repeat("⭐", (int)($avaliacao['nota'] ?? 0)) ?>
                <span>(<?= htmlspecialchars($avaliacao['nota'] ?? 0) ?>/5)</span>
            </p>

            <p class="comentario comentario-detalhe">
                “<?= htmlspecialchars($avaliacao['comentario'] ?? '') ?>”
            </p>

            <p class="data-avaliacao">
                <?= htmlspecialchars($avaliacao['data'] ?? '') ?>
            </p>
        </div>

        <?php
        $usuarioId = $_SESSION['usuario_id'] ?? 'visitante';

        $likes = is_array($avaliacao['likes'] ?? null) ? $avaliacao['likes'] : [];
        $deslikes = is_array($avaliacao['deslikes'] ?? null) ? $avaliacao['deslikes'] : [];

        $jaCurtiu = in_array($usuarioId, $likes);
        $jaDescurtiu = in_array($usuarioId, $deslikes);
        ?>

        <div class="acoes-avaliacao">
            <a class="btn-reacao like <?= $jaCurtiu ? 'ativo' : '' ?>" href="index.php?url=avaliacao/like&id=<?= urlencode($avaliacao['id']) ?>">
                Gostei <span><?= count($likes) ?></span>
            </a>

            <a class="btn-reacao deslike <?= $jaDescurtiu ? 'ativo' : '' ?>" href="index.php?url=avaliacao/deslike&id=<?= urlencode($avaliacao['id']) ?>">
                Não gostei <span><?= count($deslikes) ?></span>
            </a>

            <a class="btn btn-inline btn-secundario" href="index.php?url=avaliacao/editar&id=<?= urlencode($avaliacao['id']) ?>">
                Editar
            </a>
        </div>

        <hr class="separador">

        <div class="comentarios-area">
            <h2>Comentários</h2>

            <?php if (!empty($avaliacao['comentarios'])): ?>
                <div class="lista-comentarios">
                    <?php foreach ($avaliacao['comentarios'] as $coment): ?>
                        <div class="comentario-card">
                            <strong><?= htmlspecialchars($coment['usuario_nome'] ?? 'Usuário') ?></strong>
                            <p><?= htmlspecialchars($coment['texto'] ?? '') ?></p>
                            <small><?= htmlspecialchars($coment['data'] ?? '') ?></small>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="estado-vazio">
                    <p>Nenhum comentário ainda.</p>
                </div>
            <?php endif; ?>

            <form action="index.php?url=avaliacao/comentar" method="POST" class="form-comentario">
                <input type="hidden" name="avaliacao_id" value="<?= htmlspecialchars($avaliacao['id']) ?>">

                <label for="comentario">Adicionar comentário</label>
                <textarea name="comentario" id="comentario" rows="3" required placeholder="Escreva sua resposta..."></textarea>

                <button type="submit">Comentar</button>
            </form>
        </div>
    </section>
</div>