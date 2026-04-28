<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Meu Perfil</title>
</head>
<body>

<header>
<h1>Bem-vindo ao seu perfil, <?php echo htmlspecialchars($dadosUsuario['nome'] ?? 'Usuário'); ?>!</h1>
<a href="index.php?url=home">Voltar para a Home</a>
</header>

<main>
<section style="border: 1px solid #ccc; padding: 20px; max-width: 400px; margin-top: 20px;">
<h2>Dados da Conta</h2>
<p><strong>Nome:</strong> <?php echo htmlspecialchars($dadosUsuario['nome'] ?? ''); ?></p>
<p><strong>E-mail:</strong> <?php echo htmlspecialchars($dadosUsuario['email'] ?? ''); ?></p>
<p><strong>ID do Usuário:</strong> <?php echo htmlspecialchars($dadosUsuario['id'] ?? ''); ?></p>

<br>
<a href="index.php?url=recuperar-senha"><button>Alterar Senha</button></a>
</section>
</main>

</body>
</html>
