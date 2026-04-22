<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Editar Avaliação - Ponto Crítico</title>
</head>
<body>
    <h2>Editar Avaliação</h2>
    
    <form action="index.php?url=avaliacao/atualizar" method="POST">
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($avaliacao['id']); ?>">
        
        <div style="margin-bottom: 15px;">
            <label>Nota (1 a 5):</label><br>
            <input type="number" name="nota" min="1" max="5" required value="<?php echo htmlspecialchars($avaliacao['nota']); ?>">
        </div>
        
        <div style="margin-bottom: 15px;">
            <label>Comentário:</label><br>
            <textarea name="comentario" rows="4" cols="50" required><?php echo htmlspecialchars($avaliacao['comentario']); ?></textarea>
        </div>
        
        <button type="submit">Salvar Alterações</button>
        <a href="index.php?url=home" style="margin-left: 10px;">Cancelar</a>
    </form>
</body>
</html>
