<?php

require_once __DIR__ . '/../vendor/autoload.php';

session_start();

spl_autoload_register(function ($class) {
    $path = __DIR__ . '/../' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($path)) {
        require_once $path;
    }
});

$url = $_GET['url'] ?? 'home';

if (
    !isset($_SESSION['aceitou_termos']) &&
    $url !== 'termos' &&
    $url !== 'termos/aceitar'
) {
    header('Location: index.php?url=termos');
    exit;
}

// 1. PRIMEIRO O ROTEADOR (Lógica de processamento)
// Se houver um header() lá dentro, ele será executado antes de qualquer HTML
ob_start(); // Dica extra: Inicia o buffer de saída para evitar erros de cabeçalho

switch ($url) {
    case 'termos/aceitar':
    $dataNascimento = $_POST['data_nascimento'] ?? '';

    if (
        empty($dataNascimento) ||
        empty($_POST['aceite_termos']) ||
        empty($_POST['aceite_privacidade'])
    ) {
        header('Location: index.php?url=termos&erro=1');
        exit;
    }

    $nascimento = new DateTime($dataNascimento);
    $hoje = new DateTime();
    $idade = $hoje->diff($nascimento)->y;

    if ($idade < 13) {
        header('Location: index.php?url=termos&erro=idade');
        exit;
    }

    $_SESSION['aceitou_termos'] = true;
    $_SESSION['data_nascimento'] = $dataNascimento;
    $_SESSION['idade_usuario'] = $idade;

    header('Location: index.php?url=home');
    exit;

    case 'auth/login':
        $controller = new \App\Controllers\AuthController();
        $controller->login();
        break;

    case 'auth/registrar':
        $controller = new \App\Controllers\AuthController();
        $controller->registrar();
        break;

    case 'auth/logout':
        $controller = new \App\Controllers\AuthController();
        $controller->logout();
        break;
    
    case 'avaliacao/atualizar':
        $controller = new \App\Controllers\AvaliacaoController();
        $controller->atualizar();
        break;

    case 'avaliacao/salvar':
        $controller = new \App\Controllers\AvaliacaoController();
        $controller->salvar();
        break;

    case 'avaliacao/like':
        $controller = new \App\Controllers\AvaliacaoController();
        $controller->like();
        break;

    case 'avaliacao/deslike':
        $controller = new \App\Controllers\AvaliacaoController();
        $controller->deslike();
        break;
        
    case 'avaliacao/comentar':
    $controller = new \App\Controllers\AvaliacaoController();
    $controller->comentar();
    break;
}

// 2. DEPOIS O HTML (Menu e Views)
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ponto Crítico</title>
    <link rel="icon" type="image/png" href="/img/icone.pontocritico.png">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header class="barra-superior">
    <div class="barra-conteudo">
        <a href="index.php?url=home" class="mini-brand">
            <img src="img/nome.pontocritico.png" alt="Ponto Crítico">
            <span>Ponto Crítico</span>
        </a>

        <nav class="menu-topo">
            <a href="index.php?url=home">Home</a>

            <?php if (isset($_SESSION['usuario_id'])): ?>
                <a href="index.php?url=cadastrar-midia">Cadastrar Mídia</a>
                <a href="index.php?url=perfil">Perfil</a>
                <a href="index.php?url=auth/logout" class="menu-destaque">Sair</a>
            <?php else: ?>
                <a href="index.php?url=login">Login</a>
                <a href="index.php?url=recuperar-senha">Recuperar Senha</a>
                <a href="index.php?url=cadastro" class="menu-destaque">Cadastrar</a>
            <?php endif; ?>
        </nav>
    </div>
</header>
<hr>

<?php
// Roteador de Views (coisas que apenas exibem conteúdo)
switch ($url) {
    case 'home':
    // 1. Instancia os Controllers/Models necessários
    $midiaController = new \App\Controllers\MidiaController();
    $avModel = new \App\Models\AvaliacaoModel();

    // 2. Busca os dados (essas variáveis ficarão disponíveis no require_once abaixo)
    $midias = $midiaController->obterMidias();
    $avaliacoes = $avModel->obterAvaliacoesCompletas();

    // 3. Chama a View
    require_once __DIR__ . '/../App/Views/home.php';
    break;

    case 'termos':
    require_once __DIR__ . '/../App/Views/termos.php';
    break;

    case 'cadastro':
        require_once __DIR__ . '/../App/Views/cadastro.php';
        break;

    
    case 'login':
        require_once __DIR__ . '/../App/Views/login.php';
        break;

    case 'confirmar':
        $controller = new App\Controllers\AuthController();
        $controller->confirmar(); // Chama o método que processa o token
        break;

    case 'aviso-confirmacao':
        // Rota para a view que avisa que o e-mail foi enviado
        require_once __DIR__ . '/../App/Views/aviso-confirmacao.php';
        break;
    
    case 'recuperar-senha':
        require_once __DIR__ . '/../App/Views/Recuperar-senha.php';
        break;

    case 'auth/redefinir-senha':
        $controller = new \App\Controllers\AuthController();
        $controller->redefinirSenha();
        break;

    case 'redefinir':
    require_once __DIR__ . '/../App/Views/redefinir.php';
    break;

    case 'confirmar-redefinicao':
    $controller = new \App\Controllers\AuthController();
    $controller->confirmarRedefinicao();
    break;

    case 'avaliar':
    $controller = new \App\Controllers\AvaliacaoController();
    $controller->criar(); // Isso vai chamar o require_once para Views/avaliacao.php
    break;

    case 'cadastrar-midia':
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: index.php?url=login&erro=restrito');
            exit();
        }
        require_once __DIR__ . '/../App/Views/cadastro_midia.php';
        break;

    case 'midia/salvar':
        $controller = new \App\Controllers\MidiaController();
        $controller->salvar();
        break;
    case 'perfil':
        $controller = new \App\Controllers\AuthController();
        $controller->visualizarPerfil();
        break;

    // Rota para mostrar o formulário
case 'perfil/editar':
    $controller = new \App\Controllers\UsuarioController();
    $controller->exibirEdicao();
    break;

// Rota para processar a alteração
case 'perfil/atualizar':
    $controller = new \App\Controllers\UsuarioController();
    $controller->atualizar();
    break;

case 'avaliacao/editar':
        $controller = new \App\Controllers\AvaliacaoController();
        $controller->editar();
        break;

case 'avaliacao/ver':
    $controller = new \App\Controllers\AvaliacaoController();
    $controller->ver();
    break;
}
?>
</body>
</html>
