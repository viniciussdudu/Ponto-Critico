<h1>Bem-vindo ao Ponto Crítico!</h1>

<?php if (isset($_SESSION['usuario_id'])): ?>
    <p>Olá, <strong><?= $_SESSION['usuario_nome'] ?></strong>!</p>
    <a href="index.php?url=cadastrar-midia">Cadastrar Nova Mídia</a> |
    
    <a href="index.php?url=avaliar">Avaliar uma Mídia</a> |




   
<?php endif; ?>

<hr>

<h2>Mídias Cadastradas:</h2>
<?php if (!empty($midias)): ?>
    <ul>
        <?php foreach ($midias as $midia): ?>
            <li>
                <strong><?= htmlspecialchars($midia['titulo']) ?></strong> - 
                <?= htmlspecialchars($midia['tipo']) ?> - 
                <?= htmlspecialchars($midia['genero']) ?>
            </li>
        <?php endforeach; ?>
    </ul>
<?php else: ?>
    <p>Nenhuma mídia cadastrada ainda.</p>
<?php endif; ?>

<hr>

<h2>Avaliações Recentes:</h2>
<?php if (!empty($avaliacoes)): ?>
    <ul>
        <?php foreach ($avaliacoes as $av): ?>
            <li style="margin-bottom: 15px; border-bottom: 1px solid #eee; padding-bottom: 10px;">
                <strong><?= htmlspecialchars($av['nome_usuario']) ?></strong> avaliou 
                <strong><?= htmlspecialchars($av['titulo_midia']) ?></strong><br>
                <span>Nota: <?= str_repeat("⭐", $av['nota']) ?> (<?= $av['nota'] ?>/5)</span><br>
                <em>"<?= htmlspecialchars($av['comentario']) ?>"</em>
            </li>
        <?php endforeach; ?>
    </ul>
<?php else: ?>
    <p>Nenhuma avaliação encontrada.</p>
<?php endif; ?>

