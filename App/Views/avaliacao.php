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
    // O JavaScript agora serve EXCLUSIVAMENTE para a animação visual das estrelas clicadas
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

        // Alimenta o <input type="hidden"> que o formulário vai enviar via POST
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
            // Comentários serão carregados via API separada
            const comentariosCount = Array.isArray(av.comentarios) ? av.comentarios.length : 0;

            return `\
                <div class="avaliacao-card" data-avaliacao-id="${av.id}">\
                    <div class="avaliacao-topo">\
                        <strong>${av.usuario_nome || 'Usuário'}</strong>\
                        <span>${'★'.repeat(Math.round(av.nota || 0))} (${av.nota || 0}/5)</span>\
                    </div>\
                    <p class="comentario">${av.comentario || ''}</p>\
                    <p class="data-avaliacao">${av.data || ''}</p>\
                    <div class="comentarios-actions">\
                        <button class="btn-ver-comentarios" data-avaliacao-id="${av.id}">Ver comentários (${comentariosCount})</button>\
                    </div>\
                    <div class="comentarios-container" id="comentarios-${av.id}"></div>\
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

    // --- Comentários via API ---
    const isLoggedIn = <?= isset($_SESSION['usuario_id']) ? 'true' : 'false' ?>;

    async function carregarComentarios(avaliacaoId) {
        const container = document.getElementById(`comentarios-${avaliacaoId}`);
        if (!container) return;
        container.innerHTML = '<p>Carregando comentários...</p>';

        try {
            const resp = await fetch(`index.php?url=api/comentarios/listar&avaliacao_id=${encodeURIComponent(avaliacaoId)}`);
            const dados = await resp.json();
            if (!resp.ok) {
                container.innerHTML = `<p class="erro">${dados.mensagem || 'Erro ao carregar comentários.'}</p>`;
                return;
            }

            if (!Array.isArray(dados) || dados.length === 0) {
                container.innerHTML = '<p class="sem-comentarios">Sem comentários.</p>' + (isLoggedIn ? buildComentarioForm(avaliacaoId) : '<p><a href="index.php?url=login">Faça login</a> para comentar.</p>');
                return;
            }

            const html = dados.map(c => `\
                <div class="comentario-card">\
                    <strong>${c.usuario_nome || 'Usuário'}</strong>\
                    <p>${c.texto || ''}</p>\
                    <small>${c.data || ''}</small>\
                </div>\
            `).join('') + (isLoggedIn ? buildComentarioForm(avaliacaoId) : '<p><a href="index.php?url=login">Faça login</a> para comentar.</p>');

            container.innerHTML = html;
            // attach submit handler for this form
            const form = container.querySelector('.comentario-form');
            if (form) form.addEventListener('submit', (e) => enviarComentario(e, avaliacaoId));
        } catch (err) {
            container.innerHTML = '<p class="erro">Erro de rede ao carregar comentários.</p>';
        }
    }

    function buildComentarioForm(avaliacaoId) {
        return `\
            <form class="comentario-form" data-avaliacao-id="${avaliacaoId}">\
                <textarea name="comentario" rows="2" required placeholder="Escreva seu comentário..."></textarea>\
                <button type="submit">Enviar comentário</button>\
            </form>\
        `;
    }

    async function enviarComentario(event, avaliacaoId) {
        event.preventDefault();
        const form = event.target;
        const texto = form.querySelector('textarea[name="comentario"]').value.trim();
        if (!texto) return;

        const formData = new FormData();
        formData.append('avaliacao_id', avaliacaoId);
        formData.append('comentario', texto);

        const submitBtn = form.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.textContent = 'Enviando...';

        try {
            const resp = await fetch('index.php?url=api/comentarios/enviar', { method: 'POST', body: formData });
            const dados = await resp.json();
            if (!resp.ok) {
                mostrarMensagem(dados.mensagem || 'Erro ao enviar comentário.', 'erro');
                submitBtn.disabled = false;
                submitBtn.textContent = 'Enviar comentário';
                return;
            }

            mostrarMensagem('Comentário enviado com sucesso!');
            // recarrega comentários
            carregarComentarios(avaliacaoId);
        } catch (err) {
            mostrarMensagem('Erro de rede ao enviar comentário.', 'erro');
        } finally {
            submitBtn.disabled = false;
            submitBtn.textContent = 'Enviar comentário';
        }
    }

    // Delegation for "Ver comentários" buttons
    document.addEventListener('click', function (ev) {
        const btn = ev.target.closest('.btn-ver-comentarios');
        if (!btn) return;
        const aid = btn.getAttribute('data-avaliacao-id');
        carregarComentarios(aid);
    });

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