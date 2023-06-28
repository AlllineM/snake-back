<?php

namespace App\Controller;

use App\Entity\Fact;
use App\Entity\Reaction;
use App\Entity\UserAliker;
use App\Repository\ReactionRepository;
use App\Repository\RequestRepository;
use App\Services\ReactionFactory;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\VarDumper\VarDumper;


class ReactionController extends AlikerController
{
    /**
     * Добавить оценку факта
     * @Route("/api/v.0/reaction/add", methods={"OPTIONS", "POST"})
     * @param Request         $request
     * @param ReactionFactory $reactionFactory
     *
     * @return Response
     */
    public function add(Request $request, ReactionFactory $reactionFactory)
    {
        $params = json_decode($request->getContent(), true);
        $factId = $params['fact_id'];
        $status = $params['status'];
        $user = $this->getUser();
        $entityManager = $this->getDoctrine()->getManager();

        //Чтобы не было дублей todo: Мурррк! :)
        /** @var Reaction $reaction */
        $reaction = $this->getDoctrine()
            ->getRepository(Reaction::class)
            ->findReaction($user->getId(), $factId);

        if (!empty($reaction)) {
            $reaction = $reaction[0];
            $status = ReactionRepository::getStatus($status);

            $reaction->setStatus($status);
            $entityManager->persist($reaction);
            $entityManager->flush();

            return $this->successResponse(['reactionId' => $reaction->getId()]);
        }

        $reaction = $reactionFactory->makeReaction($user, $factId, $status);
        $entityManager->persist($reaction);
        $entityManager->flush();

        $user->setBalance($user->getBalance() + 10);
        $user->setRating($user->getRating() + 1);
        $entityManager->persist($user);
        $entityManager->flush();
        //todo: отсюда цены тоже вытащить. Мб в отдельные методы

        return $this->successResponse(['reactionId' => $reaction->getId()]);
    }

    /**
     * Выгружает все общие факты двух юзеров
     * @Route("/api/v.0/reaction/get/common", methods={"OPTIONS", "POST"})
     * @param Request $request
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

    /**
     * Выгружает карточку мэйта
     * @Route("/api/v.0/reaction/get/match-detail", methods={"OPTIONS", "POST"})
     * @param Request $request
     * @return Response
     */
    public function getMatchDetail(Request $request)
    {
        $params = json_decode($request->getContent(), true);

        $facts = $this->getDoctrine()
            ->getRepository(Reaction::class)
            ->getMatchDetail($this->getUser()->getId(), $params['alikerId']);

        return $this->successResponse($facts);
    }

    /**
     * Выгружает все оценки юзера
     * @Route("/api/v.0/reaction/get/all", methods={"OPTIONS", "GET"})
     *
     * @return Response
     */
    public function getAll()
    {
        $reactions = $this->getDoctrine()
            ->getRepository(Reaction::class)
            ->findAllReactions($this->getUser()->getId());

        return $this->successResponse($reactions);
    }
}
