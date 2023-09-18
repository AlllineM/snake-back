<?php

namespace App\Controller;

use App\Entity\Fact;
use App\Form\AddFactFormType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\UserAliker;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class LogController extends AlikerController
{
    /**
     * Получить рандомный факт
     * @Route("/api/v.0/log/create", methods={"GET", "OPTIONS"})
     *
     * @return Response|null
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function getRandom() : Response
    {
        /** @var Fact $fact */
        $fact = $this->getDoctrine()
            ->getRepository(Fact::class)
            ->findRandom($this->getUser()->getId());

        if (!$fact) return $this->successResponse(
            ['message' => 'Нет фактов без оценок'],
            404
        );

        $serializer = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);
        $data = $serializer->normalize($fact, null, [AbstractNormalizer::ATTRIBUTES => ['id', 'body']]);

        return $this->successResponse($data);
    }
}
