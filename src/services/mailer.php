<?php

namespace DazzRick\HelloServer\Services;

use DazzRick\HelloServer\Entity\User;
use DazzRick\HelloServer\Exceptions\InternalServerException;
use PHPMailer\PHPMailer\PHPMailer;

final class MailerService
{
    private static function throwException(string $additional_text = ''): void
    {
        $plus = $additional_text === '' ? '' : ': ' . $additional_text;
        throw new InternalServerException('Error while sending the verification mail' . $plus);
    }

    private static function getVerificationBody(string $code, string $name): string
    {
        $mail_body = file_get_contents(dirname(__DIR__, 2) . '/static/email/index.html');
        return str_replace(['%code%', '%name%'], [$code, $name], $mail_body);
    }

    public static function sendVerification(User $entity): string
    {
        $code = '' . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9);
        $name = $entity->getName();

        $mailer = new PHPMailer();
        $mailer->isSMTP();
        $mailer->Host = $_ENV['SMTP_HOST'];

        $mailer->SMTPAuth = true;
        $mailer->Username = $_ENV['SMTP_USER'];
        $mailer->Password = $_ENV['SMTP_PASS'];
        $mailer->SMTPSecure = 'tls';

        $mailer->Port = $_ENV['SMTP_PORT'];

        try{
            $mailer->setFrom($_ENV['SMTP_USER'], 'Hello App');
            $mailer->addAddress($entity->getEmail(), $name);
        } catch (\Exception $e)
        {
            self::throwException($e->getMessage());
        }
        $mailer->isHTML();

        $mailer->Subject = 'Verificação de Email';
        $mailer->Body = self::getVerificationBody($code, $name);
        $works = true;
        try {
            $works = $mailer->send();
        } catch (\Exception $e)
        {
            self::throwException($e->getMessage());
        }
        if(!$works)
        {
            self::throwException($mailer->ErrorInfo);
        }
        return $code;
    }
}