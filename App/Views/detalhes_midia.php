<!DOCTYPE html>
<html lang="pt-BR">
<div class="page">
    <header class="topo">
        <div>
            <h1><?php echo htmlspecialchars($midia['titulo'] ?? ''); ?></h1>
            <p class="subtitulo">
                <?php echo htmlspecialchars($midia['tipo_midia'] ?? $midia['tipo'] ?? ''); ?> • 
                <?php echo htmlspecialchars($midia['genero'] ?? ''); ?> • 
                <?php echo htmlspecialchars($midia['data_lancamento'] ?? ''); ?>
            </p>
        </div>

        <div class="acoes-topo">
            <a class="btn btn-secundario btn-inline" href="index.php?url=home">Voltar para a Home</a>
            <?php if (isset($_SESSION['usuario_id'])): ?>
                <a class="btn btn-inline" href="index.php?url=avaliar&midia_id=<?php echo urlencode($midia['id']); ?>">Avaliar Mídia</a>
            <?php endif; ?>
        </div>
    </header>

    <main class="grid-home">
        <section class="card" style="display: flex; flex-direction: column; gap: 16px;">
            <h2>Informações</h2>
            
            <div class="midia-capa-detalhe" style="align-self: center; margin-bottom: 8px;">
                <?php if (!empty($midia['capa_midia'])): ?>
                    <img src="uploads/capas_midias/<?= htmlspecialchars($midia['capa_midia']) ?>" 
                         alt="Capa de <?= htmlspecialchars($midia['titulo'] ?? 'Mídia') ?>" 
                         style="width: 100%; max-width: 200px; height: 280px; object-fit: cover; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); display: block;">
                <?php else: ?>
                    <img src="img/sem-capa.png" 
                         alt="Sem Capa" 
                         style="width: 100%; max-width: 200px; height: 280px; object-fit: cover; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); display: block;">
                <?php endif; ?>
            </div>

            <div class="dados-tecnicos">
                <p><strong>Tipo:</strong> <?php echo htmlspecialchars($midia['tipo_midia'] ?? $midia['tipo'] ?? ''); ?></p>
                <p><strong>Gênero:</strong> <?php echo htmlspecialchars($midia['genero'] ?? ''); ?></p>
                <p><strong>Lançamento:</strong> <?php echo htmlspecialchars($midia['data_lancamento'] ?? ''); ?></p>
            </div>

            <hr style="border: 0; border-top: 1px solid #eee; margin: 5px 0;">

            <h3 style="margin-top: 5px;">Sinopse</h3>
            <p style="line-height: 1.5;"><?php echo nl2br(htmlspecialchars($midia['sinopse'] ?? '')); ?></p>
        </section>

        <section class="card card-largo">
            <div class="topo-secao">
                <div>
                    <h2>Avaliações dos Usuários</h2>
                    <p class="subtitulo-secao">
                        Nota média: 
                        <strong style="font-size: 1.2em; color: #00c030;">
                            <?php echo number_format($notaMedia, 2, ',', '.'); ?>/5
                        </strong>
                        (<?php echo count($avaliacoes); ?> avaliação<?php echo count($avaliacoes) !== 1 ? 'ões' : ''; ?>)
                    </p>
                </div>
            </div>

            <?php if (!empty($avaliacoes)): ?>
                <div class="lista-avaliacoes-grid">
                    <?php foreach ($avaliacoes as $av): ?>
                        <div class="avaliacao-card">
                            <div class="avaliacao-topo">
                                <h3><?php echo htmlspecialchars($av['nome_usuario'] ?? 'Usuário Anônimo'); ?></h3>
                                <span class="badge-tipo"><?php echo htmlspecialchars($av['nota'] ?? '0'); ?>/5</span>
                            </div>

                            <p class="comentario">"<?php echo htmlspecialchars($av['comentario'] ?? ''); ?>"</p>
                            <p class="data-avaliacao"><?php echo htmlspecialchars($av['data'] ?? $av['data_registro'] ?? ''); ?></p>

                            <?php if (isset($av['likes']) || isset($av['deslikes'])): ?>
                                <div style="margin-top: 12px; font-size: 0.9rem; color: var(--muted);">
                                    👍 <?php echo count($av['likes'] ?? []); ?> • 
                                    👎 <?php echo count($av['deslikes'] ?? []); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="estado-vazio">
                    <p>Nenhuma avaliação encontrada para esta mídia.</p>
                </div>
            <?php endif; ?>
        </section>
    </main>
</div>
</html>