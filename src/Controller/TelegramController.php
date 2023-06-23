<?php

namespace App\Controller;

use App\Entity\Message;
use App\Form\SendMessageFormType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\UserAliker;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;


class TelegramController extends AlikerController
{
    /**
     * Хук, на который телега отправляет сообщения юзеров
     * @Route("/api/v.0/telegram/handle/message", methods={"POST", "GET", "OPTIONS"})
     * @param Request $request
     * @throws \Exception
     */
    public function handleMessage(Request $request)
    {
        try {
            $data = json_decode(file_get_contents("php://input"),true);
            $arrdataapi = print_r($data, true);
            file_put_contents(__DIR__ . '/../../apidataInput.txt', "Данные от бота: $arrdataapi", FILE_APPEND);

            $text = $data['message']['text'] ?? null;
            file_put_contents(__DIR__ . '/../../apidataInput.txt', '-----' . $text, FILE_APPEND);

            return $this->successResponse(['status' => 'ok']);
        } catch (\Exception $exception) {
            $error = date('d-m-Y. H-i-s') . ': ' . $exception->getMessage() . "\n";
            file_put_contents(__DIR__ . '/../../var/log/telegram_webhook_error_log.txt', $error, FILE_APPEND);
        }
    }
}
