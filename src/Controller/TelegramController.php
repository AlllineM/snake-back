<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;


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

            $text = $data['kjhk']['text'] ?? null;
            file_put_contents(__DIR__ . '/../../apidataInput.txt', '-----' . $text, FILE_APPEND);

            return $this->successResponse(['status' => 'ok']);
        } catch (\Exception $exception) {
            $error = date('d-m-Y. H-i-s') . ': ' . $exception->getMessage() . "\n";
            file_put_contents(__DIR__ . '/../../var/log/telegram_webhook_error_log.txt', $error, FILE_APPEND);
        }
    }
}
