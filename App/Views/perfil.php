<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Meu Perfil</title>
<style>
    /* ==========================================================================
       ESTILOS DAS ABAS EM CSS PURO (ORGANIZAÇÃO VERTICAL)
       ========================================================================== */
    
    /* Oculta os rádios estruturais */
    input[type="radio"][name="perfil-tabs"] {
        display: none;
    }

    /* Container das abas: Cruza a página inteira abaixo dos dados do perfil */
    .abas-navegacao {
        display: flex;
        gap: 24px;
        border-bottom: 2px solid #2a2e33;
        margin: 24px 0;
        padding-bottom: 0;
        width: 100%;
    }

    /* Estilo dos botões/labels */
    .aba-btn {
        color: #a0aab2;
        font-size: 1.1rem;
        font-weight: 600;
        padding: 10px 4px;
        cursor: pointer;
        transition: color 0.2s, border-color 0.2s;
        border-bottom: 3px solid transparent;
        margin-bottom: -2px;
    }

    .aba-btn:hover {
        color: #ffffff;
    }

    /* Oculta as seções dinâmicas por padrão */
    .conteudo-tab {
        display: none;
    }

    /* REGRA 1: Mostra o bloco correto abaixo da navegação ocupando a largura total */
    #tab-radio-avaliacoes:checked ~ .abas-conteudo .secao-tab-avaliacoes,
    #tab-radio-listas:checked ~ .abas-conteudo .secao-tab-listas {
        display: block;
    }

    /* REGRA 2: Aplica o destaque rosa na aba ativa */
    #tab-radio-avaliacoes:checked ~ .abas-navegacao .label-avaliacoes,
    #tab-radio-listas:checked ~ .abas-navegacao .label-listas {
        color: #ffffff;
        border-color: #ff477e;
    }

    /* Força os cards internos a usarem 100% de largura, sem divisão central */
    .secao-conteudo-total {
        width: 100%;
    }

    /* ==========================================================================
       ESTILOS DO GRID DE LISTAS (MÁXIMO 4 POR LINHA)
       ========================================================================== */
    .grid-listas {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 20px;
        margin-top: 15px;
        width: 100%;
    }

    .card-lista {
        background-color: #1a1d20;
        border: 1px solid #2a2e33;
        border-radius: 8px;
        transition: transform 0.2s, border-color 0.2s, background-color 0.2s;
    }

    .card-lista:hover {
        transform: translateY(-2px);
        border-color: #ff477e;
        background-color: #212529;
    }

    .card-lista-link {
        text-decoration: none;
        display: flex;
        flex-direction: column;
        padding: 20px;
        height: 100%;
        box-sizing: border-box;
    }

    .icon-pasta {
        font-size: 2rem;
        margin-bottom: 12px;
    }

    .card-lista h4 {
        color: #ffffff;
        margin: 0;
        font-size: 1.1rem;
        font-weight: 600;
        word-break: break-word;
    }

    /* Responsividade para telas menores */
    @media (max-width: 1024px) {
        .grid-listas { grid-template-columns: repeat(3, minmax(0, 1fr)); }
    }
    @media (max-width: 768px) {
        .grid-listas { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }
    @media (max-width: 480px) {
        .grid-listas { grid-template-columns: 1fr; }
    }
</style>
</head>
<body>

<div class="page">
    
    <input type="radio" name="perfil-tabs" id="tab-radio-avaliacoes" 
        <?php echo (!isset($_GET['tab']) || $_GET['tab'] !== 'listas') ? 'checked' : ''; ?>>

    <input type="radio" name="perfil-tabs" id="tab-radio-listas" 
        <?php echo (isset($_GET['tab']) && $_GET['tab'] === 'listas') ? 'checked' : ''; ?>>

    <header class="topo">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 16px;">
            <div>
                <h1>Bem-vindo ao seu perfil, <?php echo htmlspecialchars($dadosUsuario['nome'] ?? 'Usuário'); ?>!</h1>
                <p class="subtitulo">Aqui estão seus dados e todas as avaliações feitas por você.</p>
            </div>

            <div class="acoes-topo">
                <a class="btn btn-secundario btn-inline" href="index.php?url=perfil/editar">Editar Perfil</a>
                <a class="btn btn-secundario btn-inline" href="index.php?url=recuperar-senha">Alterar Senha</a>
                <a class="btn btn-secundario btn-inline" href="index.php?url=home">Voltar para a Home</a>
            </div>
        </div>
    </header>

    <section class="card" style="margin-top: 20px; width: 100%;">
        <h2>Dados da Conta</h2>
        
        <?php if (!empty($dadosUsuario['foto_perfil'])): ?>
            <img src="uploads/fotos_perfis/<?= htmlspecialchars($dadosUsuario['foto_perfil']) ?>" alt="Capa de <?= htmlspecialchars($dadosUsuario['nome']) ?>" class="capa-midia" style="width: 180px; height: 180px; object-fit: cover; border-radius: 50px; margin-bottom: 12px; display: block;">
        <?php else: ?>
            <img src="img/sem-capa.png" alt="Sem Capa" class="capa-midia" style="width: 180px; height: 180px; object-fit: cover; border-radius: 50px; margin-bottom: 12px; display: block;">
        <?php endif; ?>

        <div style="display: flex; flex-direction: column; gap: 12px; margin-top: 15px;">
            <p style="margin: 0;"><strong>Nome:</strong> <?php echo htmlspecialchars($dadosUsuario['nome'] ?? ''); ?></p>
            <p style="margin: 0;"><strong>E-mail:</strong> <?php echo htmlspecialchars($dadosUsuario['email'] ?? ''); ?></p>
            <p style="margin: 0;"><strong>Bio:</strong> <?php echo htmlspecialchars($dadosUsuario['bio'] ?? ''); ?></p>
        </div>
    </section>

    <nav class="abas-navegacao">
        <label for="tab-radio-avaliacoes" class="aba-btn label-avaliacoes">Reviews</label>
        <label for="tab-radio-listas" class="aba-btn label-listas">Lists</label>
    </nav>

    <div class="abas-conteudo">
        
        <div class="conteudo-tab secao-tab-avaliacoes secao-conteudo-total">
            <div class="topo-secao" style="margin-bottom: 20px;">
                <div>
                    <h2>Suas Avaliações</h2>
                    <p class="subtitulo-secao">As avaliações estão ordenadas da nota mais alta para a mais baixa.</p>
                </div>
                <form method="GET" action="index.php" style="display: flex; gap: 12px; flex-wrap: wrap; align-items: center;">
                    <input type="hidden" name="url" value="perfil">
                    <label for="nota">Filtrar por nota:</label>
                    <select id="nota" name="nota" style="width: 120px;">
                        <option value=""<?php if (!isset($_GET['nota']) || $_GET['nota'] === '') echo ' selected'; ?>>Todas</option>
                        <?php for ($i = 5; $i >= 1; $i--): ?>
                            <option value="<?= $i ?>"<?php if (isset($_GET['nota']) && (int) $_GET['nota'] === $i) echo ' selected'; ?>><?= $i ?></option>
                        <?php endfor; ?>
                    </select>
                    <button type="submit" class="btn btn-inline">Aplicar</button>
                </form>
            </div>

            <?php if (!empty($avaliacoesUsuario)): ?>
                <div class="lista-avaliacoes-grid">
                    <?php foreach ($avaliacoesUsuario as $avaliacao): ?>
                        <div class="avaliacao-card">
                            <div class="avaliacao-topo">
                                <h3><?php echo htmlspecialchars($avaliacao['titulo_midia'] ?? 'Mídia sem título'); ?></h3>
                                <span class="badge-tipo"><?php echo htmlspecialchars($avaliacao['nota'] ?? '0'); ?>/5</span>
                            </div>
                            <p class="comentario">“<?php echo htmlspecialchars($avaliacao['comentario'] ?? ''); ?>”</p>
                            <p class="data-avaliacao"><?php echo htmlspecialchars($avaliacao['data'] ?? ''); ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="estado-vazio">
                    <p>Você ainda não possui avaliações ou não há avaliações com a nota selecionada.</p>
                </div>
            <?php endif; ?>
        </div>

        <div class="conteudo-tab secao-tab-listas secao-conteudo-total">
            <div class="container-gerenciador">
                <h2>Minhas Listas de Mídias</h2>
                
                <div class="box-criar-lista" style="margin-top: 15px; margin-bottom: 25px;">
                    <h3>+ Criar Nova Lista</h3>
                    <form action="index.php?url=lista/criar" method="POST" class="form-inline">
                        <input type="text" name="nome" placeholder="Digite o nome da lista..." required class="input-clean">
                        <button type="submit" class="btn-sucesso">Criar</button>
                    </form>
                </div>

                <hr class="divider">

                <h3 style="margin-top: 25px; margin-bottom: 15px;">Listas Existentes</h3>
                
                <?php if (empty($listas)): ?>
                    <div class="estado-vazio">
                        <p class="text-mutado">Você ainda não criou nenhuma lista.</p>
                    </div>
                <?php else: ?>
                    <div class="grid-listas">
                        <?php foreach ($listas as $lista): ?>
                            <div class="card-lista">
                                <a href="index.php?url=lista/ver&id=<?php echo $lista['id']; ?>" class="card-lista-link">
                                    <span class="icon-pasta">📂</span>
                                    <h4><?php echo htmlspecialchars($lista['nome']); ?></h4>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </div> </div> </body>
</html>