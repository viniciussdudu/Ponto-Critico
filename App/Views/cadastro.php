<h2>Criar Conta - Ponto Crítico</h2>

<form action="index.php?url=auth/registrar" method="POST">
    <div>
        <label>Nome Completo:</label>
        <input type="text" name="nome" required placeholder="Ex: João Silva">
    </div>
    
    <div>
        <label>E-mail:</label>
        <input type="email" name="email" required placeholder="seu@email.com">
    </div>
    
    <div>
        <label>Senha:</label>
        <input type="password" name="senha" required placeholder="Mínimo 6 caracteres">
    </div>

    <button type="submit">Cadastrar</button>
</form>

<p>Já tem uma conta? <a href="index.php?url=login">Faça login aqui</a></p>