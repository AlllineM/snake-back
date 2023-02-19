<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class MailerController extends AlikerController
{
    /**
     * @Route("/email")
     * @param MailerInterface $mailer
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */
    public function sendEmail(MailerInterface $mailer) : Response
    {
        $email = (new Email())
            ->from('a-line-s@yandex.ru')
            ->to('a-line-s@yandex.ru')
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')
            //->priority(Email::PRIORITY_HIGH)
            ->subject('Time for Symfony Mailer!')
            ->text('Sending emails is fun again!')
            ->html('<p>Тут должен быть текст)</p>');

        $mailer->send($email);

        return $this->successResponse(['id' => 'qq']);
    }
}
