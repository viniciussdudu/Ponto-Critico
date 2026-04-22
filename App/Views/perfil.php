<!DOCTYPE html>
<html lang="pt-br">
<head>
<title>Meu Perfil - Ponto Crítico</title>
</head>
<body>
<h1>Perfil do  Usuário</h1>
<p><strong>Nome:</strong> <?php echo $usuario['nome']; ?></p>
<p><strong>Email:</strong> <?php echo $usuario['email']; ?></p>
<a href="index.php?url=editar_perfil">Editar Perfil</a>
</body>
</html>
