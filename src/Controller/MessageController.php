<?php

namespace App\Controller;

use App\Entity\Message;
use App\Form\SendMessageFormType;
use App\Helpers\Logger;
use App\Services\TelegramService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\UserAliker;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;


class MessageController extends AlikerController
{
    /**
     * Отправить сообщение
     * @Route("/api/v.0/message/send", methods={"POST", "OPTIONS"})
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function sendMessage(Request $request, TelegramService $telegramService, Logger $logger): Response
    {
        $data = json_decode($request->getContent(), true);

        $message = new Message();
        $form = $this->createForm(SendMessageFormType::class, $message);
        $form->submit($data);

        //todo: добавить валидацию
        if ($form->isSubmitted()) {
            /** @var UserAliker $userFrom */
            $userFrom = $this->getDoctrine()
                ->getRepository(UserAliker::class)
                ->findOneById($this->getUser()->getId());
            /** @var UserAliker $userTo */
            $userTo = $this->getDoctrine()
                ->getRepository(UserAliker::class)
                ->findOneById($data['userTo']);

            $message->setUserFrom($userFrom);
            $message->setUserTo($userTo);
            $message->setStatus(Message::NEW);
            $message->setDateTime(new \DateTime());

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($message);
            $entityManager->flush();

            $logger->errorTelegram('1 work');
            //Если не подключены уведомления в телеге, то мимо)
            if (empty($userTo->getTelegramUser())) {
                return $this->successResponse(['messageId' => $message->getId()]);
            }
            $logger->errorTelegram('2 work');
            if (!$userTo->getTelegramUser()->getIsPushAllowed()) {
                return $this->successResponse(['messageId' => $message->getId()]);
            }
            $logger->errorTelegram('3 work');

            //Шлем уведомления не чаще, чем раз в 5 минут.
            // Сейчас в lastOnline записываются только попытки уведомлений. Да, такое себе)
            // Позже будет норм)
            if (!empty($userTo->getLastOnline()) && $userTo->getLastOnline()->getTimestamp() > time() - 60 * 5) {
                return $this->successResponse(['messageId' => $message->getId()]);
            }
            $logger->errorTelegram('4 work');

            //todo: это потом сделать красиво) Вынести куда-нибудь
            $userFromName = $userFrom->getNick() ?? 'Пользователь №' . $userFrom->getId();
            $text = 'У вас личное сообщение от ' . $userFromName
                . 'Прочитать: https://aliker.ru/messages?alikerId=' . $userFrom->getId();

            $logger->errorTelegram('5 work');

            $result = $telegramService->sendMessage(
                $userTo->getTelegramUser()->getId(),
                $text
            );
            $logger->errorTelegram('6 work');

            if ($result) {
                $userTo->setLastOnline(new \DateTime());
                $entityManager->persist($userTo);
                $entityManager->flush();
            }

            $logger->errorTelegram('7 work');

//
//                if (!empty($userTo->getEmail())) {
//                    $this->sendMail($userTo, $userFrom);
//                } else {
//                    $errorMessage = 'Общие логи. Уведомление не отправлено, почему-то пустые телега и мыло' . "\n"
//                        . 'messageId: ' . $message->getId();
//                    $logger->errorTelegram($errorMessage);
//                }
//

            return $this->successResponse(['messageId' => $message->getId()]);
        } else  {
            return $this->formError();
        }
    }

    public function sendMail($userTo, $userFrom)
    {
        $userFromName = $userFrom->getNick() ?? 'Пользователь №' . $userFrom->getId();
        $htmlText = '<p>У вас личное сообщение от ' . $userFromName . '</p><br>'
            . '<a href="https://aliker.ru/messages?alikerId=' . $userFrom->getId() . '">Прочитать</a>';

        $mail = new PHPMailer(true);
        try {
            // $mail->SMTPDebug = SMTP::DEBUG_SERVER;
            $mail->isSMTP();
            $mail->Host       = $this->getParameter('app.mailer_host');
            $mail->SMTPAuth   = true;
            $mail->Username   = $this->getParameter('app.mailer_user');
            $mail->Password   = $this->getParameter('app.mailer_pass');
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = 465;

            $mail->setFrom('info@aliker.ru', 'Алайкер');
            $mail->addAddress($userTo->getEmail());

            $mail->isHTML(true);
            $mail->Subject = $userFromName . ' отправил вам личное сообщение';
            $mail->Body    = $htmlText;
            $mail->AltBody = 'У вас личное сообщение от ' . $userFromName;

            $mail->DKIM_domain = 'aliker.ru';
            $mail->DKIM_private = $this->getParameter('app.mailer_path');
            $mail->DKIM_identity = $mail->From;
            $mail->send();
        } catch (Exception $e) {
            //echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }

    /**
     * Получить общее количество непрочитанный сообщений
     * @Route("/api/v.0/message/get-unread-general", methods={"GET", "OPTIONS"})
     * @param Request $request
     */
    public function getUnreadGeneral(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $currentUserId = $this->getDoctrine()
            ->getRepository(UserAliker::class)
            ->findOneById($this->getUser()->getId());

        $data = $this->getDoctrine()
            ->getRepository(Message::class)
            ->getUnreadGeneral($currentUserId);

        return $this->successResponse($data);
    }

    /**
     * Получить количество непрочитанный сообщений отдельно для каждого юзера
     * @Route("/api/v.0/message/get-unread", methods={"GET", "OPTIONS"})
     * @param Request $request
     */
    public function getUnread(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $currentUserId = $this->getDoctrine()
            ->getRepository(UserAliker::class)
            ->findOneById($this->getUser()->getId());

        $data = $this->getDoctrine()
            ->getRepository(Message::class)
            ->getUnread($currentUserId);

        return $this->successResponse($data);
    }

    public function getChats()
    {
    }

    /**
     * Возвращает переписку с userId
     * @Route("/api/v.0/message/get", methods={"POST", "OPTIONS"})
     * @return Response
     */
    public function getMessages(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        try {
            $params = json_decode($request->getContent(), true);
            $currentUserId = $this->getDoctrine()
                ->getRepository(UserAliker::class)
                ->findOneById($this->getUser()->getId());

            $messages = $this->getDoctrine()
                ->getRepository(Message::class)
                ->getMessages($currentUserId, $params['userId']);
        } catch (\Exception $exception) {
            //Я хз, что здесь за хрень. Почему-то иногда этот урл плюет ошибку
            //todo: разобраться
        }

        return $this->successResponse($messages);
    }
}
