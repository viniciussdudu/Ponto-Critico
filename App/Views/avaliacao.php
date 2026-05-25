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

        <form id="avaliacaoForm" action="index.php?url=avaliacao/salvar" method="POST">

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

        <div class="avaliacoes-container">
            <h2>Avaliações desta mídia</h2>
            <div id="mensagemApi" role="alert" style="margin-bottom:16px; color:#333;"></div>
            <div id="avaliacoesLista">
                <p>Selecione uma mídia para ver as avaliações existentes.</p>
            </div>
        </div>
    </div>
</div>

<script>
    const estrelas = document.querySelectorAll("#rating span");
    const inputNota = document.getElementById("nota");
    const selectMidia = document.getElementById("midia_id");
    const form = document.getElementById("avaliacaoForm");
    const listaContainer = document.getElementById("avaliacoesLista");
    const mensagemApi = document.getElementById("mensagemApi");

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

    function mostrarMensagem(texto, tipo = "info") {
        mensagemApi.textContent = texto;
        mensagemApi.style.color = tipo === "erro" ? "#c0392b" : "#1d6f42";
    }

    function renderizarAvaliacoes(avaliacoes) {
        if (!avaliacoes || avaliacoes.length === 0) {
            listaContainer.innerHTML = '<p>Não há avaliações cadastradas para esta mídia.</p>';
            return;
        }

        const html = avaliacoes.map((av) => {
            const comentarios = Array.isArray(av.comentarios) && av.comentarios.length > 0
                ? `<div class="lista-comentarios">${av.comentarios.map((comentario) => `\
                    <div class="comentario-card">\
                        <strong>${comentario.usuario_nome || 'Usuário'}</strong>\
                        <p>${comentario.texto || ''}</p>\
                        <small>${comentario.data || ''}</small>\
                    </div>\
                `).join('')}</div>`
                : '<p class="sem-comentarios">Sem comentários adicionais.</p>';

            return `\
                <div class="avaliacao-card">\
                    <div class="avaliacao-topo">\
                        <strong>${av.usuario_nome || 'Usuário'}</strong>\
                        <span>${'★'.repeat(Math.round(av.nota || 0))} (${av.nota || 0}/5)</span>\
                    </div>\
                    <p class="comentario">${av.comentario || ''}</p>\
                    <p class="data-avaliacao">${av.data || ''}</p>\
                    ${comentarios}\
                </div>\
            `;
        }).join('');

        listaContainer.innerHTML = html;
    }

    async function carregarAvaliacoes(midiaId) {
        if (!midiaId) {
            listaContainer.innerHTML = '<p>Selecione uma mídia para ver as avaliações existentes.</p>';
            return;
        }

        try {
            listaContainer.innerHTML = '<p>Carregando avaliações...</p>';
            const resposta = await fetch(`index.php?url=api/avaliacoes/listar&midia_id=${encodeURIComponent(midiaId)}`);
            const dados = await resposta.json();

            if (!resposta.ok) {
                mostrarMensagem(dados.mensagem || 'Erro ao carregar avaliações.', 'erro');
                return;
            }

            renderizarAvaliacoes(dados);
        } catch (erro) {
            mostrarMensagem('Não foi possível carregar as avaliações.', 'erro');
            listaContainer.innerHTML = '<p>Erro ao carregar avaliações.</p>';
        }
    }

    async function enviarAvaliacao(event) {
        event.preventDefault();

        if (!selectMidia.value) {
            mostrarMensagem('Escolha uma mídia antes de enviar.', 'erro');
            return;
        }

        if (!inputNota.value) {
            mostrarMensagem('Selecione uma nota antes de enviar.', 'erro');
            return;
        }

        const comentario = document.getElementById("comentario").value.trim();
        if (!comentario) {
            mostrarMensagem('Escreva um comentário antes de enviar.', 'erro');
            return;
        }

        mostrarMensagem('Enviando avaliação...');

        const formData = new FormData();
        formData.append('midia_id', selectMidia.value);
        formData.append('nota', inputNota.value);
        formData.append('comentario', comentario);

        try {
            const resposta = await fetch('index.php?url=api/avaliacoes/enviar', {
                method: 'POST',
                body: formData
            });

            const dados = await resposta.json();
            if (!resposta.ok) {
                mostrarMensagem(dados.mensagem || 'Erro ao enviar avaliação.', 'erro');
                return;
            }

            mostrarMensagem('Avaliação enviada com sucesso!');
            document.getElementById('comentario').value = '';
            notaAtual = 0;
            estrelaClicada = null;
            quantidadeCliques = 0;
            atualizarVisual();
            carregarAvaliacoes(selectMidia.value);
        } catch (erro) {
            mostrarMensagem('Erro de rede ao enviar avaliação.', 'erro');
        }
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

    selectMidia.addEventListener('change', function () {
        carregarAvaliacoes(this.value);
    });

    form.addEventListener('submit', enviarAvaliacao);

    if (selectMidia.value) {
        carregarAvaliacoes(selectMidia.value);
    }
</script>

</body>
</html>