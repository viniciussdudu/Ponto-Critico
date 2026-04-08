<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Avaliações - Ponto Crítico</title>
</head>
<body>
    <div>
        <h1>Avaliações dos Usuários</h1>
        <a href="index.php?url=home">Voltar</a>
        <hr>

        <?php if (empty($avaliacoes)): ?>
            <p>Nenhuma avaliação encontrada.</p>
        <?php else: ?>
            <ul>
                <?php foreach ($avaliacoes as $av): ?>
                    <li style="margin-bottom: 20px;">
                        <strong>Mídia:</strong> <?php echo htmlspecialchars($av['midia']['titulo']); ?> 
                        (<?php echo htmlspecialchars($av['midia']['tipo']); ?> - <?php echo htmlspecialchars($av['midia']['genero']); ?>)<br>
                        
                        <strong>Nota:</strong> <?php echo htmlspecialchars($av['nota']); ?> / 5<br>
                        
                        <strong>Comentário:</strong> "<?php echo htmlspecialchars($av['comentario']); ?>"<br>
                        
                        <small><strong>Data da Avaliação:</strong> <?php echo htmlspecialchars(date('d/m/Y H:i', strtotime($av['data_registro']))); ?></small>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</body>
</html>