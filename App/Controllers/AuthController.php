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


            $dadosParaSalvar = [
                'nome' => $nome,
                'email' => $email,
                'senha' => $senha
            ];



            if ($userModel->salvar($dadosParaSalvar)) {
                header('Location: index.php?url=login&sucesso=1');
            } else {
                echo "Erro: E-mail já cadastrado!";
            }
        }
    }
    public function redefinirSenha() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'];
            $userModel = new Usuario();

            if ($userModel->buscarPorEmail($email)) {
                header('Location: index.php?url=redefinir&sucesso=1');
            } else {
                header('Location: index.php?url=recuperar-senha&erro=usuario_nao_encontrado');
            }
        }
    }
}
