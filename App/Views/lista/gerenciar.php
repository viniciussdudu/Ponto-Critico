<div class="container-gerenciador">
    <h2>Minhas Listas de Mídias</h2>
    
    <div class="box-criar-lista">
        <h3>+ Criar Nova Lista</h3>
        <form action="index.php?url=lista/criar" method="POST" class="form-inline">
    <input type="text" name="nome" placeholder="Digite o nome da lista..." required class="input-clean">
    <button type="submit" class="btn-sucesso">Criar</button>
</form>
    </div>

    <hr class="divider">

    <h3>Listas Existentes</h3>
    <?php if (empty($listas)): ?>
        <p class="text-mutado">Você ainda não criou nenhuma lista.</p>
    <?php else: ?>
        <div class="grid-listas">
            <?php foreach ($listas as $lista): ?>
                <div class="card-lista">
                    <a href="index.php?url=lista/ver&id=<?php echo $lista['id']; ?>">
                        <span class="icon-pasta">📂</span>
                        <h4><?php echo htmlspecialchars($lista['nome']); ?></h4>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>