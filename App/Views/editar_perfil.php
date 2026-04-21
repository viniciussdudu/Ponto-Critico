<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Editar Perfil - Ponto Crítico</title>
</head>
<body>
    <h1>Editar Perfil</h1>
    
    <form action="index.php?url=perfil/atualizar" method="POST">
        <div>
            <label for="nome">Nome:</label>
            <input type="text" id="nome" name="nome" 
                   value="<?php echo htmlspecialchars($usuario['nome'] ?? ''); ?>" required>
        </div>

        <div>
            <label for="email">E-mail:</label>
            <input type="email" id="email" name="email" 
                   value="<?php echo htmlspecialchars($usuario['email'] ?? ''); ?>" required>
        </div>

        <button type="submit">Salvar Alterações</button>
        <a href="index.php?url=home">Cancelar</a>
    </form>
</body>
</html>