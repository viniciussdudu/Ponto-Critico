<div class="termos-page">
    <div class="termos-card">
        <div class="termos-brand">
            <img src="img/logo2.pontocritico.png" alt="Logo Ponto Crítico">
            <h1>Ponto Crítico</h1>
            <p>Antes de continuar, confirme suas informações e aceite os termos da plataforma.</p>
        </div>

        <?php if (isset($_GET['erro'])): ?>
    <div class="mensagem-erro">
        <?php if ($_GET['erro'] === 'idade'): ?>
            Você precisa ter pelo menos 13 anos para acessar a plataforma.
        <?php else: ?>
            Informe sua data de nascimento e aceite os termos para continuar.
        <?php endif; ?>
    </div>
<?php endif; ?>

        <form action="index.php?url=termos/aceitar" method="POST">
            <label for="data_nascimento">Data de nascimento</label>
<input type="date" name="data_nascimento" id="data_nascimento" required>

            <div class="termos-box premium">
                <h2>Termos de Uso</h2>
                <p>
                    Ao utilizar o Ponto Crítico, você concorda em publicar avaliações e comentários
                    de forma respeitosa, sem conteúdo ofensivo, discriminatório, ilegal ou que viole
                    a experiência de outros usuários.
                </p>

                <h2>Política de Privacidade</h2>
                <p>
                    O sistema poderá armazenar informações como nome, e-mail, idade, avaliações,
                    curtidas, comentários e outros dados necessários para o funcionamento da plataforma.
                </p>

                <h2>Responsabilidade do Usuário</h2>
                <p>
                    Cada usuário é responsável pelo conteúdo que publica. O Ponto Crítico busca manter
                    um ambiente seguro, colaborativo e respeitoso para todos.
                </p>
            </div>

            <label class="checkbox-linha">
                <input type="checkbox" name="aceite_termos" required>
                <span>Li e aceito os Termos de Uso.</span>
            </label>

            <label class="checkbox-linha">
                <input type="checkbox" name="aceite_privacidade" required>
                <span>Li e aceito a Política de Privacidade.</span>
            </label>

            <button type="submit">Concordar e continuar</button>
        </form>
    </div>
</div>