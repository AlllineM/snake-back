<?php

namespace App\Controller;

use App\Entity\Fact;
use App\Entity\Message;
use App\Form\AddFactFormType;
use App\Form\SendMessageFormType;
use App\Repository\MessageRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\UserAliker;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class MessageController extends AlikerController
{
    /**
     * Отправить сообщение
     * @Route("/api/v.0/message/send", methods={"POST", "OPTIONS"})
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return Response
     * @throws \Exception
     */
    public function sendMessage(Request $request, MailerInterface $mailer): Response
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

            $userFromName = $userFrom->getNick() ?? 'Пользователь №' . $userFrom->getId();
            $htmlText = '<p>У вас личное сообщение от ' . $userFromName . '</p><br>'
                . '<a href="http://aliker.ru/messages?alikerId=' . $userFrom->getId() . '">Прочитать</a>';

            $email = (new Email())
                ->from('a-likers@yandex.ru')
                ->to($userTo->getEmail())
                ->subject($userFromName . ' отправил вам личное сообщение')
                ->text('У вас личное сообщение от ' . $userFromName)
                ->html($htmlText);

            $mailer->send($email);

            return $this->successResponse(['messageId' => $message->getId()]);
        } else  {
            return $this->formError();
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
