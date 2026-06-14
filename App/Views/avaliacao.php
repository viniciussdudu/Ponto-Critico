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

        <form id="avaliacaoForm" action="index.php?url=api/avaliacoes/enviar" method="POST">

            <label for="midia_id">Qual mídia você quer avaliar?</label>
            <select name="midia_id" id="midia_id" required>
                <option value="">Selecione uma mídia</option>

                <?php if (!empty($midias)): ?>
                    <?php foreach ($midias as $midia): ?>
                        <option value="<?= htmlspecialchars($midia['id_midia'] ?? $midia['id'] ?? '') ?>">
                            <?= htmlspecialchars($midia['titulo']) ?> (<?= htmlspecialchars($midia['tipo_midia'] ?? $midia['tipo'] ?? 'Mídia') ?>)
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

            <?php if (isset($_GET['erro'])): ?>
                <div id="mensagemApi" role="alert" style="margin-bottom:16px; color:#c0392b; font-weight: bold;">
                    <?php 
                        if ($_GET['erro'] === 'ja_avaliado') echo "Você já enviou uma avaliação para esta mídia.";
                        else if ($_GET['erro'] === 'dados_invalidos') echo "Preencha todos os campos corretamente.";
                        else echo "Erro ao processar a avaliação. Tente novamente.";
                    ?>
                </div>
            <?php endif; ?>

            <button type="submit">Enviar Avaliação</button>

            <p class="text-center">
                <a href="index.php?url=home">Cancelar</a>
            </p>
        </form>
    </div>
</div>

<script>
    // O JavaScript agora serve EXCLUSIVAMENTE para a animação visual das estrelas clicadas
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

        // Alimenta o <input type="hidden"> que o formulário vai enviar via POST
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