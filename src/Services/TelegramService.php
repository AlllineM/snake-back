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
     * @param int $chatId
     * @param string $text
     * @return bool
     */
    public function sendMessage($chatId, $text)
    {
        try {
            $query = [
                'chat_id' 	=> $chatId,
                'text'  	=> $text,
                'parse_mode' => 'html'
            ];
            $ch = curl_init('https://api.telegram.org/bot'. $this->botToken . '/sendMessage?' . http_build_query($query));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HEADER, false);
            $resultQuery = curl_exec($ch);
            curl_close($ch);

            if (empty($resultQuery)) {
                $errorMessage = 'Сообщение не отправлено, пустой результат курл' . "\n"
                    . 'UserID: ' . $chatId . "\n"
                    . 'Сообщение: ' . $text;
                $this->logger->errorTelegram($errorMessage);
                return false;
            }

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
        } catch (\Exception $exception) {
            $this->logger->errorTelegram($exception->getMessage());

            return false;
        }
    }

    /**
     * Функция из документации телеги
     * @param $auth_data
     * @return mixed
     */
    public function checkTelegramAuthorization($auth_data) {
        $check_hash = $auth_data['hash'];
        unset($auth_data['hash']);
        $data_check_arr = [];
        foreach ($auth_data as $key => $value) {
            $data_check_arr[] = $key . '=' . $value;
        }
        sort($data_check_arr);
        $data_check_string = implode("\n", $data_check_arr);
        $secret_key = hash('sha256', $this->botToken, true);
        $hash = hash_hmac('sha256', $data_check_string, $secret_key);
        if (strcmp($hash, $check_hash) !== 0) {
            throw new \Exception('Data is NOT from Telegram');
        }
        if ((time() - $auth_data['auth_date']) > 86400) {
            throw new \Exception('Data is outdated');
        }
        return $auth_data;
    }
}
