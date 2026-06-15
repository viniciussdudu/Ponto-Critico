<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Perfil - Ponto Crítico</title>
    <style>
        /* Estilização rápida para manter a interface clean e escura */
        body {
            background-color: #0f1112;
            color: #ffffff;
            font-family: system-ui, -apple-system, sans-serif;
            padding: 40px 20px;
            margin: 0;
        }
        .container-editar {
            max-width: 600px;
            margin: 0 auto;
            background-color: #1a1d20;
            border: 1px solid #2a2e33;
            padding: 30px;
            border-radius: 8px;
        }
        h1 {
            margin-top: 0;
            margin-bottom: 24px;
            font-size: 1.8rem;
            border-bottom: 2px solid #2a2e33;
            padding-bottom: 12px;
        }
        .form-group {
            margin-bottom: 20px;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        label {
            font-weight: 600;
            color: #a0aab2;
            font-size: 0.95rem;
        }
        input[type="text"], input[type="email"], textarea {
            width: 100%;
            padding: 12px;
            box-sizing: border-box;
            background-color: #0f1112;
            border: 1px solid #2a2e33;
            border-radius: 6px;
            color: #ffffff;
            font-size: 1rem;
            transition: border-color 0.2s;
        }
        input:focus, textarea:focus {
            outline: none;
            border-color: #ff477e; /* Destaque rosa */
        }
        input[type="file"] {
            color: #a0aab2;
            font-size: 0.9rem;
        }
        .preview-foto {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 50%;
            border: 2px solid #2a2e33;
            margin-bottom: 8px;
        }
        .botoes-acoes {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-top: 28px;
        }
        .btn-salvar {
            background-color: #ff477e;
            color: #ffffff;
            border: none;
            padding: 12px 24px;
            font-size: 1rem;
            font-weight: 600;
            border-radius: 6px;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        .btn-salvar:hover {
            background-color: #e03e6f;
        }
        .btn-cancelar {
            color: #a0aab2;
            text-decoration: none;
            font-size: 1rem;
            transition: color 0.2s;
        }
        .btn-cancelar:hover {
            color: #ffffff;
        }
        .text-mutado {
            color: #6c757d;
            font-size: 0.85rem;
        }
    </style>
</head>
<body>

<div class="container-editar">
    <h1>Editar Perfil</h1>
    
    <form action="index.php?url=perfil/atualizar" method="POST" enctype="multipart/form-data">
        
        <div class="form-group">
            <label for="nome">Nome:</label>
            <input type="text" id="nome" name="nome" 
                   value="<?php echo htmlspecialchars($usuario['nome'] ?? ''); ?>" required>
        </div>

        <div class="form-group">
            <label for="email">E-mail:</label>
            <input type="email" id="email" name="email" 
                   value="<?php echo htmlspecialchars($usuario['email'] ?? ''); ?>" required>
        </div>

        <div class="form-group">
            <label for="foto_perfil">Foto de Perfil:</label>
            
            <?php if (!empty($usuario['foto_perfil'])): ?>
                <img src="uploads/fotos_perfis/<?php echo htmlspecialchars($usuario['foto_perfil']); ?>" alt="Foto atual" class="preview-foto">
            <?php else: ?>
                <img src="img/sem-capa.png" alt="Sem Foto" class="preview-foto" style="border-radius: 50px;">
            <?php endif; ?>
            
            <input type="file" id="foto_perfil" name="foto_perfil" accept="image/png, image/jpeg, image/jpg, image/webp">
            <span class="text-mutado">Formatos suportados: PNG, JPG, JPEG e WEBP.</span>
        </div>

        <div class="form-group">
            <label for="bio">Biografia / Sobre mim:</label>
            <textarea id="bio" name="bio" rows="4" placeholder="Escreva uma breve descrição sobre você..."><?php echo htmlspecialchars($usuario['bio'] ?? ''); ?></textarea>
        </div>

        <div class="botoes-acoes">
            <button type="submit" class="btn-salvar">Salvar Alterações</button>
            <a href="index.php?url=perfil" class="btn-cancelar">Cancelar</a>
        </div>
    </form>
</div>

</body>
</html>