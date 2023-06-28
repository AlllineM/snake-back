<?php

declare(strict_types=1);

namespace App\Helpers;

class Logger
{
    const COMMON_TELEGRAM_PATH = __DIR__ . '/../../var/log/telegram/';
    const TELEGRAM_ERROR_FILE = 'telegram_webhook_error_log.txt';
    const TELEGRAM_EMPTY_FILE = 'telegram_webhook_empty_message_log.txt';

    public $date;

    public function __construct()
    {
        $this->date = date('d-m-Y. H-i-s');
    }

    public function emptyMessageTelegram($data)
    {
        $logData = print_r($data, true);
        $logData = $this->date . ': ' . "\n" . $logData . "\n";
        file_put_contents(
            self::COMMON_TELEGRAM_PATH . self::TELEGRAM_EMPTY_FILE,
            $logData,
            FILE_APPEND
        );
    }
    public function errorTelegram($message)
    {
        $error = $this->date . ': ' . $message . "\n\n";
        file_put_contents(
            self::COMMON_TELEGRAM_PATH . self::TELEGRAM_ERROR_FILE,
            $error,
            FILE_APPEND
        );
    }
}
