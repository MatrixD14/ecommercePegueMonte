<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class EnviaInfoEmail
{
    private static function EnvInfo($key)
    {
        $env = Env::get('SMTP');
        return $env[$key] ?? null;
    }
    public static function dispararEmailNotificacao($nameRemetente, $emailDestino, $SalaDestino, $textoCorpo, $id_nova_reivindicacao)
    {
        $mail = new PHPMailer(true);
        $urlAceitar = URLs . "/confirmaSolicitacaoTrocaSala?id=$id_nova_reivindicacao&status=aprovado";
        $urlRecusar = URLs . "/confirmaSolicitacaoTrocaSala?id=$id_nova_reivindicacao&status=recusado";

        try {

            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = self::EnvInfo('EMAIL');
            $mail->Password   =  self::EnvInfo('EMAILPASSWORD');
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;
            $mail->CharSet    = 'UTF-8';

            $mail->setFrom(self::EnvInfo('EMAIL'), 'Sistema de Agendamento');
            // $mail->addReplyTo('suporte@seusite.com', 'Suporte Técnico');
            $mail->addAddress($emailDestino);

            $mail->isHTML(true);
            $mail->Subject = "Nova Solicitação Troca de Sala";
            $mail->Body = "
                     <h3>Motivo do remetente:</h3>
                     <p>$textoCorpo</p>
                     <br>
                     <p>Aceita que <b>\"$nameRemetente\"</b> utilize sua <b>\"$SalaDestino\"</b> </p>
                     <br>
                     <a href='$urlAceitar' style='background-color: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>ACEITAR</a>
                     &nbsp;&nbsp;
                     <a href='$urlRecusar' style='background-color: #dc3545; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>RECUSAR</a>
                 ";
            $mail->send();
            return true;
        } catch (Exception $e) {
            echo "Erro ao enviar: {$mail->ErrorInfo}";
            return false;
        }
    }
}
