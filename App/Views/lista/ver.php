<div class="container-lista-interna">
    <a href="index.php?url=lista/gerenciar" class="btn-voltar">⬅ Voltar para as listas</a>

    <h2>Exibindo Lista: <?php echo htmlspecialchars($lista['nome']); ?></h2>

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
    <h3>Mídias Salvas nesta Lista</h3>
    <ul class="lista-itens-salvos">
        <?php if (empty($midiasDaLista)): ?>
            <li class="item-vazio">Nenhuma mídia adicionada ainda. Busque uma mídia acima!</li>
        <?php else: ?>
            <?php foreach ($midiasDaLista as $m): ?>
                <li>
                    🎬 <strong><?php echo htmlspecialchars($m['titulo']); ?></strong> <?php echo isset($m['ano']) ? "({$m['ano']})" : ''; ?>
                    
                    <a href="index.php?url=lista/remover&lista_id=<?php echo $lista['id']; ?>&midia_id=<?php echo $m['id']; ?>" class="link-remover">Remover</a>
                </li>
            <?php endforeach; ?>
        <?php endif; ?>
    </ul>
</div>