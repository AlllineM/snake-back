<?php

declare(strict_types=1);

namespace App\Services;

use App\Helpers\Logger;

class TelegramService
{
    public $botToken;
    public $adminId;
    public $logger;

    public function __construct($botToken, $adminId, Logger $logger)
    {
        $this->botToken = $botToken;
        $this->adminId = $adminId;
        $this->logger = $logger;
    }

    /**
     * @param $text
     * @return bool
     */
    public function sendMessageToAdmin($text)
    {
        return $this->sendMessage($this->adminId, $text);
    }

    /**
     * @param $chatId
     * @param $text
     * @return bool
     */
    public function sendMessage($chatId, $text)
    {
        $query = [
            'chat_id' 	=> $chatId,
            'text'  	=> $text,
        ];
        $ch = curl_init('https://api.telegram.org/bot'. $this->botToken . '/sendMessage?' . http_build_query($query));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        $resultQuery = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($resultQuery, true);

        if ($result['ok']) {
            return true;
        } else {
            $errorMessage = 'Сообщение не отправлено' . "\n"
                . 'UserID: ' . $chatId . "\n"
                . 'Сообщение: ' . $text . "\n"
                . $resultQuery;
            $this->logger->errorTelegram($errorMessage);

            return false;
        }
    }
}
