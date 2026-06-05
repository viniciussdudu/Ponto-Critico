<div class="page">
<header class="topo">
<div class="brand-area">
<img src="img/logo2.pontocritico.png" alt="Logo Ponto Critico" class="logo-home">

<div>
<h1>Ponto Crítico</h1>
<p class="subtitulo-home">Gerencie mídias e acompanhe avaliações do sistema.</p>
</div>
</div>

<div class="acoes-topo">
<?php if (isset($_SESSION['usuario_tipo']) && $_SESSION['usuario_tipo'] === 'admin'): ?>
<a class="btn" href="index.php?url=cadastrar-midia">Cadastrar Nova Mídia</a>
<?php endif; ?>
</div>

<?php if (isset($_SESSION['usuario_tipo']) && $_SESSION['usuario_tipo'] === 'admin'): ?>
<a href="index.php?url=admin/logs" class="btn-admin">Ver Logs do Sistema</a>
<?php endif; ?>
</header>

<main class="grid-home">
<section class="card">
<h2>Mídias Cadastradas</h2>

<?php if (!empty($midias)): ?>
<div class="lista-cards" id="conteinerMidias">
    <?php foreach ($midias as $midia): ?>
    <div class="item-card">
    <h3><?= htmlspecialchars($midia['titulo']) ?></h3>
    <p><strong>Tipo:</strong> <?= htmlspecialchars($midia['tipo']) ?></p>
    <p><strong>Gênero:</strong> <?= htmlspecialchars($midia['genero']) ?></p>
    <p><strong>Lançamento:</strong> <?= htmlspecialchars($midia['data_lancamento'] ?? 'Não informado') ?></p>

    <?php if (!empty($midia['sinopse'])): ?>
    <p><strong>Sinopse:</strong> <?= htmlspecialchars($midia['sinopse']) ?></p>
    <?php endif; ?>

    <main class="grid-home">
        <section class="card">
            <h2>Mídias Cadastradas</h2>
            <?php if (!empty($midias)): ?>
                <div class="lista-cards">
                    <?php foreach ($midias as $midia): ?>
                        <a href="index.php?url=midia/detalhes&id=<?= urlencode($midia['id']) ?>" style="text-decoration: none; color: inherit;">
                            <div class="item-card" style="cursor: pointer; transition: transform 0.2s;">
                                <h3><?= htmlspecialchars($midia['titulo']) ?></h3>
                                <p><strong>Tipo:</strong> <?= htmlspecialchars($midia['tipo']) ?></p>
                                <p><strong>Gênero:</strong> <?= htmlspecialchars($midia['genero']) ?></p>
                                <p><strong>Lançamento:</strong> <?= htmlspecialchars($midia['data_lancamento'] ?? 'Não informado') ?></p>

                                <?php if (!empty($midia['sinopse'])): ?>
                                    <p><strong>Sinopse:</strong> <?= htmlspecialchars($midia['sinopse']) ?></p>
                                <?php endif; ?>
                            </div>
                        </a>
                            <?php if (!empty($midia['sinopse'])): ?>
                                <p><strong>Sinopse:</strong> <?= htmlspecialchars($midia['sinopse']) ?></p>
                            <?php endif; ?>


                            <?php if (isset($_SESSION['usuario_tipo']) && $_SESSION['usuario_tipo'] === 'admin'): ?>
    <form 
        method="POST" 
        action="index.php?url=api/midias/excluir-rapida"
        onsubmit="return confirm('Tem certeza que deseja excluir esta mídia? Esta ação não pode ser desfeita.');"
        class="form-excluir-midia"
    >
    <input type="hidden" name="id" value="<?= htmlspecialchars($midia['id']) ?>">
    <button type="submit" class="btn-excluir-midia">
    Excluir mídia
    </button>
    </form>
    <?php endif; ?>
    </div>
    <?php endforeach; ?>
    </div>
    <?php else: ?>
    <div class="estado-vazio" id="conteinerMidias">
    <p>Nenhuma mídia cadastrada ainda.</p>
    </div>
    <?php endif; ?>
    </section>

        <section class="card">
             <a class="btn btn-secundario" href="index.php?url=avaliar">Avaliar Mídia</a>
            <h2>Avaliações Recentes</h2>

        

           

            <?php if (!empty($avaliacoes)): ?>
                <div class="lista-avaliacoes">
                    <?php foreach ($avaliacoes as $av): ?>
                        <div class="avaliacao-card">
                            <h3>
                                <?= htmlspecialchars($av['nome_usuario'] ?? 'Usuário') ?>
                                <span class="avaliou-texto">avaliou</span>
                                <?= htmlspecialchars($av['titulo_midia'] ?? 'Mídia') ?>
                            </h3>

                            <p class="estrelas">
                                <?= str_repeat("⭐", (int)($av['nota'] ?? 0)) ?>
                                <span>(<?= (int)($av['nota'] ?? 0) ?>/5)</span>
                            </p>

                            <p class="comentario">
                                “<?= htmlspecialchars($av['comentario'] ?? '') ?>”
                            </p>

                            <div class="acoes-avaliacao">
    <a href="index.php?url=avaliacao/ver&id=<?= urlencode($av['id']) ?>" class="btn btn-inline">
        Ver Avaliação
    </a>

    <a href="index.php?url=avaliacao/editar&id=<?= urlencode($av['id']) ?>" class="btn btn-inline btn-secundario">
        Editar
    </a>
</div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="estado-vazio">
                    <p>Nenhuma avaliação encontrada.</p>
                </div>
            <?php endif; ?>
        </section>
    </main>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const inputBusca = document.getElementById('inputBuscaMidia');
    const inputGenero = document.getElementById('inputBuscaGenero');
    const btnBusca = document.getElementById('btnBuscaMidia');
    const btnGenero = document.getElementById('btnBuscaGenero');
    const conteinerMidias = document.getElementById('conteinerMidias');

    const ehAdmin = <?= (isset($_SESSION['usuario_tipo']) && $_SESSION['usuario_tipo'] === 'admin') ? 'true' : 'false' ?>;

    if (!inputBusca || !inputGenero || !btnBusca || !btnGenero || !conteinerMidias) return;

    // Função que faz a requisição à API (permanece com a lógica acumulativa idêntica)
    function filtrarMidias() {
        const termoTitulo = inputBusca.value;
        const termoGenero = inputGenero.value;

        const url = `index.php?url=api/midias/listar&titulo=${encodeURIComponent(termoTitulo)}&genero=${encodeURIComponent(termoGenero)}`;

        fetch(url)
        .then(response => response.json())
        .then(midias => {
            conteinerMidias.className = 'lista-cards';
        conteinerMidias.innerHTML = '';

        if (midias.length === 0) {
            conteinerMidias.className = 'estado-vazio';
        conteinerMidias.innerHTML = '<p>Nenhuma mídia encontrada com os filtros aplicados.</p>';
        return;
        }

        midias.forEach(midia => {
            const itemCard = document.createElement('div');
            itemCard.className = 'item-card';

        const escaparHTML = (string) => {
            if (!string) return '';
        return string.replace(/[&<>"']/g, m => ({
            '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'
        })[m]);
        };

        let sinopseHtml = '';
        if (midia.sinopse && midia.sinopse.trim() !== '') {
            sinopseHtml = `<p><strong>Sinopse:</strong> ${escaparHTML(midia.sinopse)}</p>`;
        }

        let botaoExcluirHtml = '';
        if (ehAdmin) {
            botaoExcluirHtml = `
            <form
            method="POST"
            action="index.php?url=api/midias/excluir-rapida"
            onsubmit="return confirm('Tem certeza que deseja excluir esta mídia?');"
            class="form-excluir-midia"
            >
            <input type="hidden" name="id" value="${escaparHTML(midia.id)}">
            <button type="submit" class="btn-excluir-midia">
            Excluir mídia
            </button>
            </form>
            `;
        }

        itemCard.innerHTML = `
        <h3>${escaparHTML(midia.titulo)}</h3>
        <p><strong>Tipo:</strong> ${escaparHTML(midia.tipo)}</p>
        <p><strong>Gênero:</strong> ${escaparHTML(midia.genero)}</p>
        <p><strong>Lançamento:</strong> ${escaparHTML(midia.data_lancamento || 'Não informado')}</p>
        ${sinopseHtml}
        ${botaoExcluirHtml}
        `;
        conteinerMidias.appendChild(itemCard);
        });
        })
        .catch(erro => console.error('Erro ao buscar mídias:', erro));
    }

    // 1. EVENTO DE CLIQUE: Dispara ao clicar em qualquer um dos botões de lupa
    btnBusca.addEventListener('click', filtrarMidias);
    btnGenero.addEventListener('click', filtrarMidias);

    // 2. EVENTO DE TECLADO (ENTER): Permite que o usuário aperte Enter para pesquisar
    const dispararComEnter = (e) => {
        if (e.key === 'Enter') {
            filtrarMidias();
        }
    };
    inputBusca.addEventListener('keydown', dispararComEnter);
    inputGenero.addEventListener('keydown', dispararComEnter);
});
</script>
