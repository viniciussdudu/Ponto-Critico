<div class="container">
    <div class="card card-largo">
        <h1>Cadastrar Nova Mídia</h1>
        <p class="subtitulo">Adicione uma nova mídia ao sistema</p>

        <?php if (!empty($erro)): ?>
            <div class="mensagem-erro">
                <?= htmlspecialchars($erro) ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['sucesso'])): ?>
            <div class="mensagem-sucesso">
                Mídia cadastrada com sucesso!
            </div>
        <?php endif; ?>

        <form method="POST" action="index.php?url=midia/salvar" enctype="multipart/form-data">

            <div>
                <label for="titulo">Título:</label>
                <input type="text" id="titulo" name="titulo" required>
            </div>

            <div>
                <label for="tipo">Tipo:</label>
                <select id="tipo" name="tipo" required>
                    <option value="">-- Selecione --</option>
                    <option value="Filme">Filme</option>
                    <option value="Série">Série</option>
                    <option value="Jogo">Jogo</option>
                    <option value="Livro">Livro</option>
                    <option value="Podcast">Podcast</option>
                    <option value="Música">Música</option>
                </select>
            </div>

            <div>
                <label for="genero">Gênero:</label>
                <select id="genero" name="genero" required>
                    <option value="">-- Selecione --</option>
                    <option value="Terror">Terror</option>
                    <option value="Ação">Ação</option>
                    <option value="Drama">Drama</option>
                    <option value="Comédia">Comédia</option>
                    <option value="Romance">Romance</option>
                    <option value="Fantasia">Fantasia</option>
                    <option value="Suspense">Suspense</option>
                    <option value="Documentário">Documentário</option>
                    <option value="Animação">Animação</option>
                </select>
            </div>

            <div>
                <label for="data_lancamento">Data de Lançamento:</label>
                <input type="date" id="data_lancamento" name="data_lancamento" required>
            </div>

            <div class="campo-form">
                <label for="imagem">Capa / Imagem da Mídia:</label>
                <input type="file" id="imagem" name="imagem" accept="image/*">
            </div>

            <div>
                <label for="sinopse">Sinopse:</label>
                <textarea id="sinopse" name="sinopse" required></textarea>
            </div>

            <div class="botoes-form" style="display:flex; gap:12px; flex-wrap:wrap; margin-top: 15px;">
                <a href="index.php?url=home" class="btn btn-secundario btn-inline">Voltar</a>
                <button type="submit" class="btn">Salvar Mídia</button>
            </div>
        </form>

        <p class="text-center" style="margin-top: 20px;">
            <a href="index.php?url=home">Voltar para a Home</a>
        </p>
    </div>
</div>