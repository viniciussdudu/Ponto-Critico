<h2>Recuperar Acesso</h2>
<form action="index.php?url=auth/redefinir-senha" method="POST">
<label for="email">E-mail cadastrado:</label>
<input type="email" name="email" id="email" required placeholder="seu@email.com">
<button type="submit">Verificar E-mail</button>
</form>

<?php if (isset($_GET['erro'])): ?>
<p style="color: red;">E-mail não encontrado em nossa base!</p>
<?php endif; ?>
