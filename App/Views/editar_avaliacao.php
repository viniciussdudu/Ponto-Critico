<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Editar Avaliação - Ponto Crítico</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<div class="container">
    <div class="card">
        <h1>Editar Avaliação</h1>
        <p class="subtitulo">Atualize sua nota e comentário</p>

        <form action="index.php?url=avaliacao/atualizar" method="POST">
            <input type="hidden" name="id" value="<?= htmlspecialchars($avaliacao['id']) ?>">

            <label>Sua Nota</label>

            <input 
                type="hidden" 
                name="nota" 
                id="nota" 
                value="<?= htmlspecialchars($avaliacao['nota'] ?? '') ?>" 
                required
            >

            <div class="rating-click" id="rating">
                <span data-value="1">★</span>
                <span data-value="2">★</span>
                <span data-value="3">★</span>
                <span data-value="4">★</span>
                <span data-value="5">★</span>
            </div>

            <label for="comentario">Comentário</label>
            <textarea name="comentario" id="comentario" rows="4" required><?= htmlspecialchars($avaliacao['comentario']) ?></textarea>

            <button type="submit">Salvar Alterações</button>

            <p class="text-center">
                <a href="index.php?url=home">Cancelar</a>
            </p>
        </form>
    </div>
</div>

<script>
    const estrelas = document.querySelectorAll("#rating span");
    const inputNota = document.getElementById("nota");

    let notaAtual = parseFloat(inputNota.value) || 0;
    let estrelaClicada = null;
    let quantidadeCliques = 0;

    function atualizarVisual() {
        estrelas.forEach((estrela) => {
            const valor = Number(estrela.dataset.value);

            estrela.classList.remove("full", "half");

            if (notaAtual >= valor) {
                estrela.classList.add("full");
            } else if (notaAtual === valor - 0.5) {
                estrela.classList.add("half");
            }
        });

        inputNota.value = notaAtual > 0 ? notaAtual : "";
    }

    estrelas.forEach((estrela) => {
        estrela.addEventListener("click", function () {
            const valor = Number(this.dataset.value);

            if (estrelaClicada === valor) {
                quantidadeCliques++;
            } else {
                estrelaClicada = valor;
                quantidadeCliques = 1;
            }

            if (quantidadeCliques === 1) {
                notaAtual = valor;
            } else if (quantidadeCliques === 2) {
                notaAtual = valor - 0.5;
            } else {
                notaAtual = 0;
                estrelaClicada = null;
                quantidadeCliques = 0;
            }

            atualizarVisual();
        });
    });

    atualizarVisual();
</script>

</body>
</html>