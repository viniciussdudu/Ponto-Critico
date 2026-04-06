<?php

namespace App\Controllers;
use App\Models\Usuario;

class AuthController {
    public function registrar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nome = $_POST['nome'];
            $email = $_POST['email'];
            $senha = $_POST['senha'];

            $userModel = new Usuario();
            if ($userModel->cadastrar($nome, $email, $senha)) {
                header('Location: index.php?url=login&sucesso=1');
            } else {
                echo "Erro: E-mail já cadastrado!";
            }
        }
    }
}