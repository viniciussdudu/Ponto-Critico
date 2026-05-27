<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Painel Admin - Logs de Acesso</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f6f9; color: #333; margin: 20px; }
        .container { max-width: 1000px; margin: 0 auto; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        h1 { color: #2c3e50; border-bottom: 2px solid #ecf0f1; padding-bottom: 10px; }
        .btn-voltar { display: inline-block; padding: 10px 15px; background-color: #34495e; color: #fff; text-decoration: none; border-radius: 4px; margin-bottom: 20px; }
        .btn-voltar:hover { background-color: #2c3e50; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #2980b9; color: white; }
        tr:hover { background-color: #f5f5f5; }
        .badge { padding: 5px 10px; border-radius: 4px; font-size: 12px; font-weight: bold; background-color: #fff; color: black; }
    </style>
</head>
<body>

<div class="container">
    <a href="index.php?url=home" class="btn-voltar">← Voltar para Home</a>
    
    <h1> Histórico e Logs de Acessos Recentes</h1>

    <table>
        <thead>
            <tr>
                <th>Usuário (E-mail)</th>
                <th>Data e Hora</th>
                <th>Endereço IP</th>
                <th>Ação / Evento</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($logs)): ?>
                <tr>
                    <td colspan="4" style="text-align: center; color: #7f8c8d;">Nenhum log de acesso registrado ainda.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($logs as $log): ?>
                    <tr>
                        <strong><td><?= htmlspecialchars($log['usuario_email']) ?></td></strong>
                        <td><?= htmlspecialchars($log['data_hora']) ?></td>
                        <td><code><?= htmlspecialchars($log['ip']) ?></code></td>
                        <td><span class="badge"><?= htmlspecialchars($log['evento']) ?></span></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>