<?php
/** @var array $midias */
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Nova Avaliação</title>
</head>
<body>
    <h1>Cadastrar Avaliação</h1>

    <form action="index.php?url=avaliacao/salvar" method="POST">
        <div>
            <label for="midia_id">Escolha a mídia:</label><br>
            <select name="midia_id" id="midia_id" required>
                <option value="">Selecione</option>
                <?php foreach ($midias as $midia): ?>
                    <option value="<?= htmlspecialchars($midia['id']) ?>">
                        <?= htmlspecialchars($midia['titulo']) ?> - <?= htmlspecialchars($midia['tipo']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <br>

        <div>
            <label for="nota">Nota (1 a 5 estrelas):</label><br>
            <select name="nota" id="nota" required>
                <option value="">Selecione</option>
                <option value="1">1 estrela</option>
                <option value="2">2 estrelas</option>
                <option value="3">3 estrelas</option>
                <option value="4">4 estrelas</option>
                <option value="5">5 estrelas</option>
            </select>
        </div>

        <br>

        <div>
            <label for="comentario">Comentário:</label><br>
            <textarea name="comentario" id="comentario" rows="5" cols="40" required></textarea>
        </div>

        <br>

        <button type="submit">Salvar Avaliação</button>
    </form>

    <br>
    <a href="index.php?url=home">Voltar para a Home</a>
</body>
</html>