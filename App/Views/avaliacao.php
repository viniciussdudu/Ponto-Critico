<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Nova Avaliação - Ponto Crítico</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<div class="container">
    <div class="card">
        <h1>Nova Avaliação</h1>
        <p class="subtitulo">Escolha uma mídia e registre sua opinião</p>

        <form action="index.php?url=avaliacao/salvar" method="POST">

            <label for="midia_id">Qual mídia você quer avaliar?</label>
            <select name="midia_id" id="midia_id" required>
                <option value="">Selecione uma mídia</option>

                <?php if (!empty($midias)): ?>
                    <?php foreach ($midias as $midia): ?>
                        <option value="<?= htmlspecialchars($midia['id']) ?>">
                            <?= htmlspecialchars($midia['titulo']) ?> (<?= htmlspecialchars($midia['tipo']) ?>)
                        </option>
                    <?php endforeach; ?>
                <?php else: ?>
                    <option value="" disabled>Nenhuma mídia cadastrada</option>
                <?php endif; ?>
            </select>

            <label>Sua Nota</label>

            <input type="hidden" name="nota" id="nota" required>

            <div class="rating-click" id="rating">
                <span data-value="1">★</span>
                <span data-value="2">★</span>
                <span data-value="3">★</span>
                <span data-value="4">★</span>
                <span data-value="5">★</span>
            </div>

            <label for="comentario">Comentário</label>
            <textarea name="comentario" id="comentario" rows="4" required placeholder="Escreva sua opinião..."></textarea>

            <button type="submit">Enviar Avaliação</button>

            <p class="text-center">
                <a href="index.php?url=home">Cancelar</a>
            </p>
        </form>
    </div>
</div>

<script>
    const estrelas = document.querySelectorAll("#rating span");
    const inputNota = document.getElementById("nota");

    let notaAtual = 0;
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
</script>

</body>
</html>