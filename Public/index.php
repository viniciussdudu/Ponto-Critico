<?php

// 1. Autoload simples (Para carregar as classes das pastas automaticamente)
spl_autoload_register(function ($class_name) {
    $file = __DIR__ . '/../' . str_replace('\\', '/', $class_name) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

// 2. Iniciar Sessão (Essencial para Login - RF01)
session_start();

// 3. Capturar a rota da URL (Se não houver, vai para a 'home')
$rota = $_GET['url'] ?? 'home';

// 4. O Roteador Principal
switch ($rota) {
    case 'home':
        // Exibe a lista de mídias
        $controller = new \App\Controllers\MidiaController();
        $midias = $controller->obterMidias();
        //Busca as avaliações
        $avModel = new \App\Models\AvaliacaoModel();
        $avaliacoes = $avModel->obterAvaliacoesCompletas();

        echo "<h1>Bem-vindo ao Ponto Crítico!</h1>";
        echo "<a href='index.php?url=midia/criar'>Cadastrar Nova Mídia</a>";
        
        if (!empty($midias)) {
            echo "<h2>Mídias Cadastradas:</h2>";
            echo "<ul>";
            foreach ($midias as $midia) {
                echo "<li>";
                echo "<strong>" . htmlspecialchars($midia['titulo']) . "</strong> - ";
                echo htmlspecialchars($midia['tipo']) . " - ";
                echo htmlspecialchars($midia['genero']);
                echo "</li>";
            }
            echo "</ul>";
        } else {
            echo "<p>Nenhuma mídia cadastrada ainda.</p>";
        }

        echo "<hr><h2>Avaliações Recentes:</h2>";
        if (!empty($avaliacoes)) {
            echo "<ul>";
            foreach ($avaliacoes as $av) {
                echo "<li style='margin-bottom: 15px; border-bottom: 1px solid #eee; padding-bottom: 10px;'>";
                echo "<strong>" . htmlspecialchars($av['nome_usuario']) . "</strong> avaliou ";
                echo "<strong>" . htmlspecialchars($av['titulo_midia']) . "</strong><br>";
                echo "<span>Nota: " . str_repeat("⭐", $av['nota']) . " (" . $av['nota'] . "/5)</span><br>";
                echo "<em>\"" . htmlspecialchars($av['comentario']) . "\"</em>";
                echo "</li>";
            }
            echo "</ul>";
        } else {
            echo "<p>Nenhuma avaliação encontrada.</p>";
        }
        break;

    case 'login':
        // Renderiza a view de login
        require_once __DIR__ . '/../app/Views/login.php';
        break;

    case 'cadastro':
        // Renderiza a view de cadastro
        require_once __DIR__ . '/../app/Views/cadastro.php';
        break;

    case 'auth/registrar':
        // Rota que processa o formulário de cadastro
        $controller = new \App\Controllers\AuthController();
        $controller->registrar();
        break;

    case 'auth/logar':
        // Rota que processa o formulário de login
        $controller = new \App\Controllers\AuthController();
        $controller->logar();
        break;

    case 'midia/criar':
        // Rota que exibe o formulário de cadastro de mídia
        $controller = new \App\Controllers\MidiaController();
        $controller->criar();
        break;

    case 'midia/salvar':
        // Rota que processa o formulário de cadastro de mídia
        $controller = new \App\Controllers\MidiaController();
        $controller->salvar();
        break;

    case 'recuperar-senha':
        // 1. Rota para exibir o formulário
        require_once __DIR__ . '/../App/Views/Recuperar-senha.php';
        break;

    case 'auth/redefinir-senha':
        // 2. Rota que processa o formulário (o action do seu form)
        $controller = new \App\Controllers\AuthController();
        $controller->redefinirSenha();
        break;

    case 'redefinir':
        // 3. Tela de sucesso (conforme seu requisito)
        echo "<h1>E-mail validado!</h1>";
        echo "<p>O fluxo para redefinir a senha seria iniciado aqui.</p>";
        echo "<a href='index.php?url=login'>Voltar para o Login</a>";
        break;

    default:
        // Página 404
        http_response_code(404);
        echo "<h1>404 - Página não encontrada</h1>";
        break;
}
