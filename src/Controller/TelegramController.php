<?php

namespace App\Controller;

use App\Helpers\Logger;
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
    public function handleMessage(Request $request, Logger $logger)
    {
        try {
            $requestData = json_decode(file_get_contents("php://input"),true);
            $arrdataapi = print_r($requestData, true);
            file_put_contents(__DIR__ . '/../../apidataInput.txt', "Данные от бота: $arrdataapi", FILE_APPEND);

            $text = $requestData['sdf']['text'] ?? null;
            file_put_contents(__DIR__ . '/../../apidataInput.txt', '-----' . $text, FILE_APPEND);

            switch ($text) {
                case null:
                    $logger->emptyMessageTelegram($requestData);
                    break;
            }

            return $this->successResponse(['status' => 'ok']);
        } catch (\Exception $exception) {
            $logger->errorTelegram($exception->getMessage());
        }
    }
}
