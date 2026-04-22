<div class="login-container">
    <h2>Login - Ponto Crítico</h2>

    <?php if (isset($_GET['erro'])): ?>
        <p style="color: red;">
            <?php 
                echo ($_GET['erro'] == 'restrito') 
                    ? "Você precisa estar logado para acessar essa página." 
                    : "E-mail ou senha incorretos!";
            ?>
        </p>
    <?php endif; ?>

    <?php if (isset($_GET['sucesso'])): ?>
        <p style="color: green;">Cadastro realizado! Agora é só entrar.</p>
    <?php endif; ?>

    <form action="index.php?url=auth/login" method="POST">
        <div>
            <label>E-mail:</label><br>
            <input type="email" name="email" required placeholder="seu@email.com">
        </div>
        <br>
        <div>
            <label>Senha:</label><br>
            <input type="password" name="senha" required placeholder="********">
        </div>
        <br>
        <button type="submit">Entrar</button>
    </form>

    <p>Ainda não tem conta? <a href="index.php?url=cadastro">Cadastre-se aqui</a></p>
    <p><a href="index.php?url=recuperar-senha">Esqueci minha senha</a></p>
</div>