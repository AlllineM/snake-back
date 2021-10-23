<?php

namespace App\Controller;

use App\Entity\Fact;
use App\Entity\Reaction;
use App\Entity\UserAliker;
use App\Repository\ReactionRepository;
use App\Repository\RequestRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\VarDumper\VarDumper;


class ReactionController extends AlikerController
{
    //todo: Еще сделать, чтобы не обавлялись дубли. Мурррк! :)
    /**
     * Добавить оценку факта
     * @Route("/api/v.0/reaction/add", methods={"OPTIONS", "POST"})
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function add(Request $request)
    {
        $params = json_decode($request->getContent(), true);
        $status = ReactionRepository::getStatus($params['status']);

        $user = $this->getUser();
        $user->setBalance($user->getBalance() + 10);
        $user->setRating($user->getRating() + 1);
        //todo: отсюда цены тоже вытащить. Мб в отдельные методы

        $fact = $this->getDoctrine()
            ->getRepository(Fact::class)
            ->findOneById($params['fact_id']);

        $reaction = new Reaction();
        $reaction->setUserAliker($user);
        $reaction->setFact($fact);
        $reaction->setStatus($status);
        $reaction->setDateTime(new \DateTime());

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($reaction);
        $entityManager->flush();

        return $this->successResponse(['reactionId' => $reaction->getId()]);
    }

    /**
     * Выгружает все общие факты двух юзеров
     * @Route("/api/v.0/reaction/get/common", methods={"OPTIONS", "POST"})
     * @return Response
     */
    public function getCommonFacts(Request $request)
    {
        $params = json_decode($request->getContent(), true);

        $facts = $this->getDoctrine()
            ->getRepository(Reaction::class)
            ->getCommonLikes($this->getUser()->getId(), $params['alikerId']);

        return $this->successResponse($facts);
    }
}
