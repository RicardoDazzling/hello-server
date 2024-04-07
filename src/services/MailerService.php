<?php

namespace DazzRick\HelloServer\Services;

use DazzRick\HelloServer\Entity\User;
use DazzRick\HelloServer\Exceptions\InternalServerException;
use Hybridauth\Provider\Google as HAdapter;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Email;
use Exception;

final class MailerService
{
    private static function readJson(): array
    {
        $content = file_get_contents(dirname(__DIR__, 2) . '/static/json/gmail.json');
        return (array) json_decode($content);
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

        $json = self::readJson();

        try {
            $transport = Transport::fromDsn('gmail+smtp://'
                .urlencode($_ENV['USER_GMAIL'])
                .':'
                .urlencode($json['token']['access_token'])
                .'@default');

            $mailer = new Mailer($transport);

            $message = (new Email())
                ->from('Hello <'.$_ENV['USER_GMAIL'].'>')
                ->to($entity->getEmail())
                ->subject('Verificação de email')
                ->html(self::getVerificationBody($code, $name));

            // Send the message
            $mailer->send($message);

        } catch (Exception $e) {
            if (in_array($e->getCode(), ['535', '334'])) {

                $response = self::getAdapter()->refreshAccessToken([
                    "grant_type" => "refresh_token",
                    "refresh_token" => $json['token']['refresh_token'],
                    "client_id" => $json['client_id'],
                    "client_secret" => $json['client_secret'],
                ]);

                $data = (array)json_decode($response);
                $data['refresh_token'] = $json['token']['refresh_token'];

                self::updateToken($data);

                self::sendVerification($entity);
            }
            else throw $e;
        }
        return $code;
    }

    public static function isEmpty(): bool { return empty(self::readJson()['token']); }

    public static function updateToken(array $token): void
    {
        $json_content = self::readJson();
        $json_content['token'] = $token;
        file_put_contents(dirname(__DIR__, 2) . '/static/json/gmail.json', json_encode($json_content));
    }

    public static function getAdapter(): HAdapter
    {
        $json_content = self::readJson();
        $config = [
            'callback' => $json_content['redirect_uri'],
            'keys'     => [
                'id' => $json_content['client_id'],
                'secret' => $json_content['client_secret']
            ],
            'scope'    => $json_content['scope'],
            'authorize_url_parameters' => [
                'approval_prompt' => 'force', // to pass only when you need to acquire a new refresh token.
                'access_type' => 'offline'
            ]
        ];
        return new HAdapter( $config );
    }

    public static function callback(): void { self::updateToken(self::getAdapter()->getAccessToken()); }
}