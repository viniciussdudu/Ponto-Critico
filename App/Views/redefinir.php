<form action="index.php?url=auth/confirmar-redefinicao" method="POST">
    <input type="hidden" name="email" value="<?= $_GET['email'] ?? '' ?>">
    
    <label>Nova Senha:</label>
    <input type="password" name="nova_senha" required>
    <button type="submit">Salvar Nova Senha</button>
</form>