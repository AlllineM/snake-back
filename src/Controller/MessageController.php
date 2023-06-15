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


class MessageController extends AlikerController
{
    /**
     * Отправить сообщение
     * @Route("/api/v.0/message/send", methods={"POST", "OPTIONS"})
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function sendMessage(Request $request): Response
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

            //$this->sendMail($userTo, $userFrom);

            $token = $this->getParameter('app.telegram_token');

            $getQuery = array(
                "chat_id" 	=> 333773228,
                "text"  	=> "Привет)",
            );
            $ch = curl_init("https://api.telegram.org/bot". $token ."/sendMessage?" . http_build_query($getQuery));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HEADER, false);
            $resultQuery = curl_exec($ch);
            curl_close($ch);

            var_dump($resultQuery); die();

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
            //  $mail->DKIM_selector = 'phpmailer';
            //$mail->DKIM_passphrase = '';
            $mail->DKIM_identity = $mail->From;


            $mail->send();
            //  echo 'Message has been sent';
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
        $params = json_decode($request->getContent(), true);
        $currentUserId = $this->getDoctrine()
            ->getRepository(UserAliker::class)
            ->findOneById($this->getUser()->getId());

        $messages = $this->getDoctrine()
            ->getRepository(Message::class)
            ->getMessages($currentUserId, $params['userId']);

        return $this->successResponse($messages);

    }
}
