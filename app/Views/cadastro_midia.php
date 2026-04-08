<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Mídia - Ponto Crítico</title>
</head>
<body>
    <div>
        <h1>Cadastrar Mídia</h1>

        <?php if (isset($erro)): ?>
            <p><?php echo $erro; ?></p>
        <?php endif; ?>

        <?php if (isset($_GET['sucesso'])): ?>
            <p>Mídia cadastrada com sucesso!</p>
        <?php endif; ?>

        <form method="POST" action="index.php?url=midia/salvar">
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

            <div>
                <label for="sinopse">Sinopse:</label>
                <textarea id="sinopse" name="sinopse" required></textarea>
            </div>

            <div>
                <a href="index.php?url=home">Voltar</a>
                <button type="submit">Salvar Mídia</button>
            </div>
        </form>
    </div>
</body>
</html>
