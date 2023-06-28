<?php

namespace App\Controller;

use App\Entity\TelegramUser;
use App\Helpers\Logger;
use App\Services\TelegramService;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;


class TelegramController extends AlikerController
{
    const BASE_URL = 'https://aliker.ru';

    /**
     * Хук, на который телега отправляет сообщения юзеров
     * @Route("/api/v.0/telegram/handle/message", methods={"POST", "GET", "OPTIONS"})
     * @param Request $request
     * @throws \Exception
     */
    public function handleMessage(Request $request, Logger $logger, TelegramService $telegramService)
    {
        try {
            $requestData = json_decode(file_get_contents("php://input"),true);
            $arrdataapi = print_r($requestData, true);
            file_put_contents(__DIR__ . '/../../apidataInput.txt', "Данные от бота: $arrdataapi", FILE_APPEND);
            $text = $requestData['message']['text'] ?? null;
            file_put_contents(__DIR__ . '/../../apidataInput.txt', '-----' . $text, FILE_APPEND);

            $chatId = $requestData['chat']['id'];

            /** @var $telegramUser TelegramUser */
            $telegramUser = $this->getDoctrine()
                ->getRepository(TelegramUser::class)
                ->findOneById($chatId);

            if (empty($telegramUser)) {
                $telegramService->sendMessage($chatId, 'Чтобы включить бота, войдите через телеграм на ' . self::BASE_URL);
                return $this->successResponse(['status' => 'ok']);
            }

            $entityManager = $this->getDoctrine()->getManager();

            switch ($text) {
                case null:
                    //сюда прилетает в том числе вебхук, когда перезапускают юзера. Как минимум) ПУсть пока будет тут)
                    //Проверить, летит ли, когда юзер совсем новый
                    $logger->emptyMessageTelegram($requestData);
                    $telegramService->sendMessageToAdmin('Почему-то пустое сообщение с вебхука. Инфа в логах на серваке');
                    break;
                case '/start':
                    $telegramUser->setIsPushAllowed(true);
                    $entityManager->persist($telegramUser);
                    $entityManager->flush();
                    $telegramService->sendMessage($telegramUser->getId(), 'Отлично, бот запущен. Вернуться: ' . self::BASE_URL);
                    break;
                case '/start_push':
                    $telegramUser->setIsPushAllowed(true);
                    $entityManager->persist($telegramUser);
                    $entityManager->flush();
                    $telegramService->sendMessage($telegramUser->getId(), 'Уведомления включены. Вернуться: ' . self::BASE_URL);
                    break;
                case '/stop_push':
                    $telegramUser->setIsPushAllowed(false);
                    $entityManager->persist($telegramUser);
                    $entityManager->flush();
                    $text = 'Уведомления отключены. ' . "\n"
                        . 'Внимание: уведомления пока работают только через этого бота' . "\n"
                        . 'Вернуться: ' . self::BASE_URL;
                    $telegramService->sendMessage($telegramUser->getId(), $text);
                    break;
            }

            return $this->successResponse(['status' => 'ok']);

        } catch (\Exception $exception) {
            $logger->errorTelegram($exception->getMessage());
        }
    }
}
