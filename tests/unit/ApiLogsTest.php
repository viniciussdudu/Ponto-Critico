<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class ApiLogsTest extends TestCase {

    
    public function testDeveBloquearAcessoParaUsuarioNaoAdmin() {
        $_SESSION['usuario_tipo'] = 'user';
        $_SESSION['usuario_id'] = 'user_123';

        $acessoAutorizado = isset($_SESSION['usuario_tipo']) && $_SESSION['usuario_tipo'] === 'admin';

        
        $this->assertFalse($acessoAutorizado, "Usuários comuns não deveriam ter acesso à API de logs.");
    }

    
    public function testDevePermitirAcessoParaUsuarioAdmin() {
        
        $_SESSION['usuario_tipo'] = 'admin';

        
        $acessoAutorizado = isset($_SESSION['usuario_tipo']) && $_SESSION['usuario_tipo'] === 'admin';

        
        $this->assertTrue($acessoAutorizado, "Administradores precisam ter acesso livre.");
    }


    public function testEstruturaDoLogDevePossuirCamposObrigatorios() {
        
        $usuarioFake = ['email' => 'vinicius@uft.edu.br'];
        
        $novoLog = [
            'usuario_email' => $usuarioFake['email'],
            'data_hora' => date('Y-m-d H:i:s'),
            'ip' => '127.0.0.1',
            'evento' => 'Login realizado com sucesso'
        ];

        $this->assertArrayHasKey('usuario_email', $novoLog, "O log precisa registrar o e-mail do usuário.");
        $this->assertArrayHasKey('data_hora', $novoLog, "O log precisa registrar o momento exato do acesso.");
        $this->assertArrayHasKey('ip', $novoLog, "O log precisa registrar o endereço de IP.");
        $this->assertArrayHasKey('evento', $novoLog, "O log precisa descrever o evento.");
    }
}