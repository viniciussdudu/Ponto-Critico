<?php

namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class EmailService {
    public static function enviar($destinatario, $assunto, $corpo) {
        // Criamos apenas UM objeto
        $mail = new PHPMailer(true);

        try {
            // Configurações do Servidor (Mailtrap)
            $mail->SMTPDebug = 0; 
            $mail->isSMTP();
            $mail->Host       = 'sandbox.smtp.mailtrap.io';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'bc405ae1c44889';
            $mail->Password   = '24f56260a34ea7';
            $mail->Port       = 2525;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; 

            // Remetente e Destinatário
            $mail->setFrom('no-reply@pontocritico.com.br', 'Ponto Crítico');
            $mail->addAddress($destinatario);

            // Conteúdo do E-mail
            $mail->isHTML(true);
            $mail->CharSet = 'UTF-8';
            $mail->Subject = $assunto;
            $mail->Body    = $corpo;

            // Envia o objeto que foi configurado!
            return $mail->send();
            
        } catch (Exception $e) {
            // Se der erro, isso vai imprimir na tela por causa do exit
            echo "Erro ao enviar e-mail: {$mail->ErrorInfo}";
            exit; 
        }
    }
}