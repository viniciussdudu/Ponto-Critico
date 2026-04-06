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
        // Aqui chamaria o Controller da Home (Arthur)
        echo "<h1>Bem-vindo ao Ponto Crítico!</h1><p>Em breve: Lista de mídias.</p>";
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

    default:
        // Página 404
        http_response_code(404);
        echo "<h1>404 - Página não encontrada</h1>";
        break;
}