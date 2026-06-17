<style>
    /* ==========================================================================
       ESTILOS DO GRID DE MÍDIAS (MÁXIMO 4 POR LINHA)
       ========================================================================== */
    .grid-midias {
    display: grid;
    /* auto-fill: preenche a linha ao máximo. 
       130px: a largura mínima de cada card (metade do tamanho padrão de ~260px).
       1fr: distribui o espaço restante igualmente entre eles. */
    grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
    gap: 16px; /* Espaçamento entre as mídias */
    padding: 0;
    margin-top: 20px;
    list-style: none;
    width: 100%;
}

    /* Estrutura do Card de Mídia */
    .card-midia {
        background-color: #1a1d20;
        border: 1px solid #2a2e33;
        border-radius: 8px;
        overflow: hidden; /* Garante que a imagem respeite o border-radius do card */
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        transition: transform 0.2s, border-color 0.2s;
    }

    .card-midia:hover {
        transform: translateY(-4px);
        border-color: #ff477e; /* Destaque rosa no hover */
    }

    /* Container da Capa: Força a proporção padrão de poster de filme (2:3) */
.container-capa {
    width: 100%;
    aspect-ratio: 2 / 3; /* Proporção padrão de cinema (Largura: 2, Altura: 3) */
    background-color: #0f1112;
    position: relative;
    overflow: hidden;
}

/* Imagem da Capa */
.capa-imagem {
    width: 100%;
    height: 100%;
    object-fit: cover; /* Garante que preencha o espaço sem distorcer */
    display: block;
}
    /* Bloco de Informações abaixo da imagem */
    .card-info {
        padding: 16px;
        display: flex;
        flex-direction: column;
        gap: 12px;
        flex-grow: 1;
        justify-content: space-between;
    }

    .card-info h4 {
        color: #ffffff;
        margin: 0;
        font-size: 1.05rem;
        font-weight: 600;
        line-height: 1.4;
        word-break: break-word;
    }

    .ano-midia {
        color: #a0aab2;
        font-size: 0.9rem;
        font-weight: 400;
    }

    /* Botão de Excluir/Remover Mídia */
    .btn-remover-card {
        display: block;
        text-align: center;
        background-color: #2a2e33;
        color: #ff4d4d; /* Vermelho suave para aviso */
        text-decoration: none;
        padding: 10px;
        border-radius: 6px;
        font-size: 0.9rem;
        font-weight: 600;
        transition: background-color 0.2s, color 0.2s;
        margin-top: auto; /* Empurra o botão sempre para o rodapé do card */
    }

    .btn-remover-card:hover {
        background-color: #ff4d4d;
        color: #ffffff;
    }

    /* Item Vazio */
    .item-vazio {
        grid-column: 1 / -1; /* Ocupa todas as colunas se estiver vazio */
        text-align: center;
        padding: 40px;
        background-color: #1a1d20;
        border: 2px dashed #2a2e33;
        border-radius: 8px;
        color: #a0aab2;
    }

    /* Responsividade */
    @media (max-width: 1400px) {
    .grid-midias { grid-template-columns: repeat(6, minmax(0, 1fr)); } /* 6 por linha em telas comuns */
}
</style>

<div class="container-lista-interna">
    <a href="index.php?url=perfil&tab=listas" class="btn-voltar" style="text-decoration: none; display: inline-block; margin-bottom: 20px;">⬅ Voltar para o Perfil</a>

    <!-- ALTERAÇÃO AQUI: Alinhamento flex para acomodar o novo botão de exclusão -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; margin-bottom: 20px;">
        <h2 style="margin: 0;">Exibindo Lista: <?php echo htmlspecialchars($lista['nome']); ?></h2>
        
        <form action="index.php?url=lista/excluir" method="POST" onsubmit="return confirm('Tem certeza absoluta que deseja excluir a lista \'<?php echo htmlspecialchars($lista['nome']); ?>\'?');" style="margin: 0;">
            <input type="hidden" name="lista_id" value="<?php echo $lista['id']; ?>">
            <button type="submit" class="btn-remover-card" style="margin: 0; padding: 10px 20px; cursor: pointer; border: none;">
                ❌ Excluir Lista
            </button>
        </form>
    </div>

    <div class="box-adicionar-midia">
        <h3>Adicionar Nova Mídia</h3>
        
        <form action="index.php?url=lista/adicionar-midia" method="POST">
            <input type="hidden" name="lista_id" value="<?php echo $lista['id']; ?>">

            <div class="busca-row">
                <select name="midia_id" required class="select-busca">
                    <option value="">Digite ou selecione o nome da mídia...</option>
                    <?php foreach ($todasAsMidias as $midia): ?>
                        <option value="<?php echo $midia['id']; ?>">
                            <?php echo htmlspecialchars($midia['titulo']); ?> <?php echo !empty($midia['data_lancamento']) ? "(" . substr($midia['data_lancamento'], 0, 4) . ")" : ''; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                
                <button type="submit" class="btn-add">Adicionar</button>
            </div>
        </form>
    </div>

    <h3 style="margin-top: 30px;">Mídias Salvas nesta Lista</h3>

    <ul class="grid-midias">
        <?php if (empty($midiasDaLista)): ?>
            <li class="item-vazio">Nenhuma mídia adicionada ainda. Busque uma mídia acima!</li>
        <?php else: ?>
           <?php foreach ($midiasDaLista as $midia): ?>
    <li class="card-midia">
        
        <div class="container-capa">
    <?php if (!empty($midia['capa_midia'])): ?>
        <?php 
        // Como o ponto de partida real é o index.php na raiz, chamamos a pasta direto
        $caminhoCapa = (strpos($midia['capa_midia'], 'http') === 0) 
            ? $midia['capa_midia'] 
            : 'uploads/capas_midias/' . $midia['capa_midia']; 
        ?>
        <img src="<?php echo htmlspecialchars($caminhoCapa); ?>" alt="Capa de <?php echo htmlspecialchars($midia['titulo']); ?>" class="capa-imagem">
    <?php else: ?>
        <img src="img/sem-capa.png" alt="Sem Capa" class="capa-imagem">
    <?php endif; ?>
</div>

        <div class="card-info">
            <div>
                <h4>
                    <?php echo htmlspecialchars($midia['titulo']); ?>
                    <?php if (isset($midia['ano'])): ?>
                        <span class="ano-midia">(<?php echo htmlspecialchars($midia['ano']); ?>)</span>
                    <?php elseif (!empty($midia['data_lancamento'])): ?>
                        <span class="ano-midia">(<?php echo substr($midia['data_lancamento'], 0, 4); ?>)</span>
                    <?php endif; ?>
                </h4>
            </div>
            
            <a href="index.php?url=lista/remover&lista_id=<?php echo $lista['id']; ?>&midia_id=<?php echo $midia['id']; ?>" class="btn-remover-card" onclick="return confirm('Tem certeza que deseja remover esta mídia da lista?');">
                Remover
            </a>
        </div>
    </li>
<?php endforeach; ?>
        <?php endif; ?>
    </ul>
</div>