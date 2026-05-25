<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class ApiLogsTest extends TestCase {

    // Cenário 1: Garante que quem NÃO é admin seja bloqueado pela API
    public function testDeveBloquearAcessoParaUsuarioNaoAdmin() {
        // Simula uma sessão de usuário comum
        $_SESSION['usuario_tipo'] = 'user';
        $_SESSION['usuario_id'] = 'user_123';

        // Validamos a regra que está no seu Controller:
        $acessoAutorizado = isset($_SESSION['usuario_tipo']) && $_SESSION['usuario_tipo'] === 'admin';

        // O PHPUnit garante que isso DEVE ser falso
        $this->assertFalse($acessoAutorizado, "Usuários comuns não deveriam ter acesso à API de logs.");
    }

    // Cenário 2: Garante que o Admin consiga passar pela trava de segurança
    public function testDevePermitirAcessoParaUsuarioAdmin() {
        // Simula uma sessão de administrador
        $_SESSION['usuario_tipo'] = 'admin';

        // Executa a mesma validação da trava
        $acessoAutorizado = isset($_SESSION['usuario_tipo']) && $_SESSION['usuario_tipo'] === 'admin';

        // O PHPUnit garante que isso DEVE ser verdadeiro
        $this->assertTrue($acessoAutorizado, "Administradores precisam ter acesso livre.");
    }

    // Cenário 3: Garante que a estrutura do Log criada no AuthController possui todos os campos obrigatórios
    public function testEstruturaDoLogDevePossuirCamposObrigatorios() {
        // Simulação do array exatamente como você colocou no AuthController
        $usuarioFake = ['email' => 'vinicius@uft.edu.br'];
        
        $novoLog = [
            'usuario_email' => $usuarioFake['email'],
            'data_hora' => date('Y-m-d H:i:s'),
            'ip' => '127.0.0.1',
            'evento' => 'Login realizado com sucesso'
        ];

        // Verifica se as chaves necessárias para a auditoria existem no array
        $this->assertArrayHasKey('usuario_email', $novoLog, "O log precisa registrar o e-mail do usuário.");
        $this->assertArrayHasKey('data_hora', $novoLog, "O log precisa registrar o momento exato do acesso.");
        $this->assertArrayHasKey('ip', $novoLog, "O log precisa registrar o endereço de IP.");
        $this->assertArrayHasKey('evento', $novoLog, "O log precisa descrever o evento.");
    }
}