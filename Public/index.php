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

    default:
        // Página 404
        http_response_code(404);
        echo "<h1>404 - Página não encontrada</h1>";
        break;
}