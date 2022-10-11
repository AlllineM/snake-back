<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\UserAliker;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Entity\Request as MyRequest;


/**
 * Класс для заявок
 * Class RequestController
 * @package App\Controller
 */
class RequestController extends AlikerController
{
    /**
     * Выгружает всех друзей юзера
     * @Route("/api/v.0/request/get-accepted", methods={"OPTIONS", "GET"})
     * @return Response
     */
    public function getAccepted()
    {
        $result = $this->getDoctrine()
            ->getRepository(MyRequest::class)
            ->getAccepted($this->getUser()->getId());

        return $this->successResponse($result);
    }

    /**
     * Выгружает все активные входящие заявки текущего юзера
     * @Route("/api/v.0/request/get-ins", methods={"OPTIONS", "GET"})
     * @return Response|null
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function getIn()
    {
        $result = $this->getDoctrine()
            ->getRepository(MyRequest::class)
            ->getIns($this->getUser()->getId());

        return $this->successResponse($result);
    }

    /**
     * Выгружает все активные исходящие заявки юзера
     * @Route("/api/v.0/request/get-outs", methods={"OPTIONS", "GET"})
     * @return Response
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function getOut()
    {
        $result = $this->getDoctrine()
            ->getRepository(MyRequest::class)
            ->getOuts($this->getUser()->getId());

        return $this->successResponse($result);
    }

    /**
     * Создание заявки
     * @Route("/api/v.0/request/add", methods={"OPTIONS", "POST"})
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return Response
     * @throws \Exception
     */
    public function add(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $params = json_decode($request->getContent(), true);

        $userFrom = $this->getUser();

        $balance = $userFrom->getBalance();
        if ($balance < 900) {
            $error = 'Не хватает монет. Баланс ' . $balance;
            $this->errorResponse($error);
        }

        $userTo = $this->getDoctrine()
            ->getRepository(UserAliker::class)
            ->findOneById($params['userTo']);

        //todo: "цены" куда-нибудь сохранить в одно место
        $userFrom->setBalance($balance - 900);
        $userFrom->setRating($userFrom->getRating() + 15);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($userFrom);
        $entityManager->flush();

        $request = new MyRequest();
        $request->setUserFrom($userFrom);
        $request->setUserTo($userTo);
        $request->setStatus(MyRequest::WAITING);
        $request->setDatetime(new \DateTime());
        $entityManager->persist($request);
        $entityManager->flush();

        //todo: уведомление юзеверю о том, что ему заявка

        return $this->successResponse(['requestId' => $request->getId()]);
    }

    /**
     * Принять заявку
     * @Route("/api/v.0/request/accept", methods={"OPTIONS", "POST"})
     * @param Request $request
     * @return Response
     */
    public function accept(Request $request)
    {
       $params = json_decode($request->getContent(), true);

        $request = $this->getDoctrine()
            ->getRepository(Request::class)
            ->findOneById($params['requestId']);

        $request->setStatus(MyRequest::ACCEPTED);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($request);
        $entityManager->flush();

        //todo: отправить уведомление

        return $this->successResponse(['message' => 'Заявка принята']);
    }

    /**
     * Отклонить заявку
     * @Route("/api/v.0/request/deny", methods={"OPTIONS", "POST"})
     * @param Request $request
     * @return Response
     */
    public function deny(Request $request)
    {
       $params = json_decode($request->getContent(), true);

        $request = $this->getDoctrine()
            ->getRepository(Request::class)
            ->findOneById($params['requestId']);

        $request->setStatus(MyRequest::DENIED);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($request);
        $entityManager->flush();

        //todo: отправить уведомление

        return $this->successResponse(['message' => 'Заявка отклонена']);
    }
}
