<div class="page">
    <header class="topo">
        <div class="brand-area">
            <img src="img/logo2.pontocritico.png" alt="Logo Ponto Critico" class="logo-home">

            <div>
                <h1>Ponto Crítico</h1>
                <p class="subtitulo-home">Gerencie mídias e acompanhe avaliações do sistema.</p>
            </div>
        </div>

        <div class="acoes-topo">
            <a class="btn" href="index.php?url=cadastrar-midia">Cadastrar Nova Mídia</a>
            <a class="btn btn-secundario" href="index.php?url=cadastro">Criar Conta</a>
            <a class="btn btn-secundario" href="index.php?url=recuperar-senha">Recuperar Acesso</a>
        </div>
    </header>

    <main class="grid-home">
        <section class="card">
            <h2>Mídias Cadastradas</h2>

            <?php if (!empty($midias)): ?>
                <div class="lista-cards">
                    <?php foreach ($midias as $midia): ?>
                        <div class="item-card">
                            <h3><?= htmlspecialchars($midia['titulo']) ?></h3>
                            <p><strong>Tipo:</strong> <?= htmlspecialchars($midia['tipo']) ?></p>
                            <p><strong>Gênero:</strong> <?= htmlspecialchars($midia['genero']) ?></p>
                            <p><strong>Lançamento:</strong> <?= htmlspecialchars($midia['data_lancamento'] ?? 'Não informado') ?></p>

                            <?php if (!empty($midia['sinopse'])): ?>
                                <p><strong>Sinopse:</strong> <?= htmlspecialchars($midia['sinopse']) ?></p>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="estado-vazio">
                    <p>Nenhuma mídia cadastrada ainda.</p>
                </div>
            <?php endif; ?>
        </section>

        <section class="card">
            <h2>Avaliações Recentes</h2>

            <?php if (!empty($avaliacoes)): ?>
                <div class="lista-avaliacoes">
                    <?php foreach ($avaliacoes as $av): ?>
                        <div class="avaliacao-card">
                            <h3>
                                <?= htmlspecialchars($av['nome_usuario'] ?? 'Usuário') ?>
                                <span class="avaliou-texto">avaliou</span>
                                <?= htmlspecialchars($av['titulo_midia'] ?? 'Mídia') ?>
                            </h3>

                            <p class="estrelas">
                                <?= str_repeat("⭐", (int)($av['nota'] ?? 0)) ?>
                                <span>(<?= (int)($av['nota'] ?? 0) ?>/5)</span>
                            </p>

                            <p class="comentario">
                                “<?= htmlspecialchars($av['comentario'] ?? '') ?>”
                            </p>

                            <a href="index.php?url=avaliacao/editar&id=<?= urlencode($av['id']) ?>" class="btn btn-inline btn-secundario">
                                Editar Avaliação
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="estado-vazio">
                    <p>Nenhuma avaliação encontrada.</p>
                </div>
            <?php endif; ?>
        </section>
    </main>
</div>