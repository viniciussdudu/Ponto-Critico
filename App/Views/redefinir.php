<!-- Dentro da sua View de redefinir senha -->
<form action="index.php?url=confirmar-redefinicao" method="POST">
    <!-- Captura o token da URL e coloca no formulário -->
    <input type="hidden" name="token" value="<?php echo $_GET['token'] ?? ''; ?>">
    
    <label>Digite sua nova senha:</label>
    <input type="password" name="nova_senha" required>
    
    <button type="submit">Atualizar Senha</button>
</form>