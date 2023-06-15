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
     * Отправить сообщение
     * @Route("/api/v.0/telegram/handle/message", methods={"POST", "GET", "OPTIONS"})
     * @param Request $request
     * @throws \Exception
     */
    public function sendMessage(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        var_dump($data); die();
    }
}
