<?php

namespace App\Models;

class Usuario {
    private $filePath = __DIR__ . '/../../data/usuarios.json';

     // =====================Salvar
    public function salvar($dados) {
        $usuarios = $this->listarTodos();
        if (!isset($dados['id'])) {
            $dados['id'] = uniqid('user_'); 
            $dados['senha'] = password_hash($dados['senha'], PASSWORD_DEFAULT);
            $usuarios[] = $dados;
        } else {
            foreach ($usuarios as $key => $user) {
                if ($user['id'] === $dados['id']) {
                    $usuarios[$key] = array_merge($user, $dados);
                    break;
                }
            }
        }

        return $this->persistir($usuarios);
    }


    //======================================Buscar Email
    public function buscarPorEmail($email) {
        $usuarios = $this->listarTodos();
        foreach ($usuarios as $user) {
            if ($user['email'] === $email) {
                return $user;
            }
        }
        return null;
    }

    //==========================Listar todos
    public function listarTodos() {
        if (!file_exists($this->filePath)) {
            return [];
        }
        $conteudo = file_get_contents($this->filePath);
        return json_decode($conteudo, true) ?? [];
    }

   
    private function persistir($lista) {
        return file_put_contents($this->filePath, json_encode($lista, JSON_PRETTY_PRINT));
    }

    public function atualizarSenha($email, $novaSenha) {
    $usuarios = json_decode(file_get_contents(__DIR__ . '/../../data/usuarios.json'), true);
    
    foreach ($usuarios as &$usuario) {
        if ($usuario['email'] === $email) {
            // Criptografa a nova senha antes de salvar
            $usuario['senha'] = password_hash($novaSenha, PASSWORD_DEFAULT);
            
            // Salva o array atualizado de volta no arquivo
            file_put_contents(__DIR__ . '/../../data/usuarios.json', json_encode($usuarios, JSON_PRETTY_PRINT));
            return true;
        }
    }
    return false;
}

}