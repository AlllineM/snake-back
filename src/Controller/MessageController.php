<?php

namespace App\Controller;

use App\Entity\Fact;
use App\Entity\Message;
use App\Form\AddFactFormType;
use App\Form\SendMessageFormType;
use App\Repository\MessageRepository;
use Symfony\Component\HttpFoundation\Response;
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
    public function sendMessage(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $data = json_decode($request->getContent(), true);

        $message = new Message();
        $form = $this->createForm(SendMessageFormType::class, $message);
        $form->submit($data);

        //todo: добавить валидацию
        if ($form->isSubmitted()) {
            $userFrom = $this->getDoctrine()
                ->getRepository(UserAliker::class)
                ->findOneById($this->getUser()->getId());
            $userTo = $this->getDoctrine()
                ->getRepository(UserAliker::class)
                ->findOneById($data['userTo']);

            $message->setUserFrom($userFrom);
            $message->setUserTo($userTo);
            $message->setDateTime(new \DateTime());

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($message);
            $entityManager->flush();

            return $this->successResponse(['messageId' => $message->getId()]);
        } else  {
            return $this->formError();
        }
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
            ->getRepository(MessageRepository::class)
            ->getMessages($currentUserId, $params['userId']);

        return $this->successResponse($messages);

    }

    /**
     * Получить рандомное высказывание
     * @Route("/api/v.0/fact/get-random", methods={"GET", "OPTIONS"})
     * @param Request $request
     * @return Response|null
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function getRandom(Request $request)
    {
        $fact = $this->getDoctrine()
            ->getRepository(Fact::class)
            ->findRandom($this->getUser()->getId());

        if (!$fact) return $this->errorResponse('Что-то пошло не так, ни один факт не найден');

        $serializer = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);
        $data = $serializer->normalize($fact, null, [AbstractNormalizer::ATTRIBUTES => ['id', 'body']]);

        return $this->successResponse($data);
    }


}
