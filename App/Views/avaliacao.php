<h2>Nova Avaliação</h2>

<form action="index.php?url=avaliacao/salvar" method="POST">
    
    <label for="midia_id">Qual mídia você quer avaliar?</label><br>
    <select name="midia_id" id="midia_id" required>
        <option value="">-- Selecione uma mídia --</option>
        
        <?php if (!empty($midias)): ?>
            <?php foreach ($midias as $midia): ?>
                <option value="<?= $midia['id'] ?>">
                    <?= htmlspecialchars($midia['titulo']) ?> (<?= htmlspecialchars($midia['tipo']) ?>)
                </option>
            <?php endforeach; ?>
        <?php else: ?>
            <option value="" disabled>Nenhuma mídia cadastrada</option>
        <?php endif; ?>
    </select>

    <br><br>

    <label>Sua Nota:</label><br>
    <select name="nota" required>
        <option value="5">⭐⭐⭐⭐⭐ (5)</option>
        <option value="4">⭐⭐⭐⭐ (4)</option>
        <option value="3">⭐⭐⭐ (3)</option>
        <option value="2">⭐⭐ (2)</option>
        <option value="1">⭐ (1)</option>
    </select>

    <br><br>

    <label>Comentário:</label><br>
    <textarea name="comentario" rows="4" cols="50" required placeholder="Escreva sua opinião..."></textarea>

    <br><br>
    <button type="submit">Enviar Avaliação</button>
    <a href="index.php?url=home">Cancelar</a>
</form>