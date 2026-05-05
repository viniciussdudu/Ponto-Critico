<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Meu Perfil</title>
</head>
<body>

<div class="page">
    <header class="topo">
        <div>
            <h1>Bem-vindo ao seu perfil, <?php echo htmlspecialchars($dadosUsuario['nome'] ?? 'Usuário'); ?>!</h1>
            <p class="subtitulo">Aqui estão seus dados e todas as avaliações feitas por você.</p>
        </div>

        <div class="acoes-topo">
            <a class="btn btn-secundario btn-inline" href="index.php?url=perfil/editar">Editar Perfil</a>
            <a class="btn btn-secundario btn-inline" href="index.php?url=recuperar-senha">Alterar Senha</a>
            <a class="btn btn-secundario btn-inline" href="index.php?url=home">Voltar para a Home</a>
        </div>
    </header>

    <main class="grid-home">
        <section class="card">
            <h2>Dados da Conta</h2>
            <p><strong>Nome:</strong> <?php echo htmlspecialchars($dadosUsuario['nome'] ?? ''); ?></p>
            <p><strong>E-mail:</strong> <?php echo htmlspecialchars($dadosUsuario['email'] ?? ''); ?></p>
            <p><strong>ID do Usuário:</strong> <?php echo htmlspecialchars($dadosUsuario['id'] ?? ''); ?></p>
        </section>

        <section class="card card-largo">
            <div class="topo-secao">
                <div>
                    <h2>Suas Avaliações</h2>
                    <p class="subtitulo-secao">As avaliações estão ordenadas da nota mais alta para a mais baixa.</p>
                </div>
                <form method="GET" action="index.php" style="display: flex; gap: 12px; flex-wrap: wrap; align-items: center;">
                    <input type="hidden" name="url" value="perfil">
                    <label for="nota">Filtrar por nota:</label>
                    <select id="nota" name="nota" style="width: 120px;">
                        <option value=""<?php if (!isset($_GET['nota']) || $_GET['nota'] === '') echo ' selected'; ?>>Todas</option>
                        <?php for ($i = 5; $i >= 1; $i--): ?>
                            <option value="<?= $i ?>"<?php if (isset($_GET['nota']) && (int) $_GET['nota'] === $i) echo ' selected'; ?>><?= $i ?></option>
                        <?php endfor; ?>
                    </select>
                    <button type="submit" class="btn btn-inline">Aplicar</button>
                </form>
            </div>

            <?php if (!empty($avaliacoesUsuario)): ?>
                <div class="lista-avaliacoes-grid">
                    <?php foreach ($avaliacoesUsuario as $avaliacao): ?>
                        <div class="avaliacao-card">
                            <div class="avaliacao-topo">
                                <h3><?php echo htmlspecialchars($avaliacao['titulo_midia'] ?? 'Mídia sem título'); ?></h3>
                                <span class="badge-tipo"><?php echo htmlspecialchars($avaliacao['nota'] ?? '0'); ?>/5</span>
                            </div>
                            <p class="comentario">“<?php echo htmlspecialchars($avaliacao['comentario'] ?? ''); ?>”</p>
                            <p class="data-avaliacao"><?php echo htmlspecialchars($avaliacao['data'] ?? ''); ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="estado-vazio">
                    <p>Você ainda não possui avaliações ou não há avaliações com a nota selecionada.</p>
                </div>
            <?php endif; ?>
        </section>
    </main>
</div>

</body>
</html>
