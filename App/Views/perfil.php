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

<div style="margin-top: 20px; display: flex; align-items: center; gap: 20px;"> 
    <!-- O 'gap: 20px' garante que eles nunca fiquem grudados -->

    <!-- Botão Principal -->
    <a href="index.php?url=perfil/editar" style="text-decoration: none;">
        <button style="padding: 10px 20px; background-color: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;">
            Editar Perfil
        </button>
    </a>

    <!-- Botão de Senha (Vermelho e sem fundo) -->
    <a href="index.php?url=recuperar-senha" style="text-decoration: none;">
        <button style="background: transparent; border: 1px solid #d9534f; color: #d9534f; padding: 8px 16px; font-size: 0.85em; cursor: pointer; border-radius: 4px;">
            Alterar Senha
        </button>
    </a>
</div>
</section>
</main>

</body>
</html>
