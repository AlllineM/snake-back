<?php

namespace App\Controller;

use App\Entity\Fact;
use App\Entity\Level;
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

class LevelController extends AlikerController
{
    /**
     * Выгружает все базовые уровни
     * @Route("/api/v.0/level/get/all-base", methods={"GET", "OPTIONS"})
     *
     * @return Response|null
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function getAllBase() : Response
    {
        /** @var Level $levels */
        $levels = $this->getDoctrine()
            ->getRepository(Level::class)
            ->getAllBase();

        if (!$levels) return $this->errorResponse('Базовых уровней почему-то нет');

        $serializer = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);
        $data = $serializer->normalize($levels, null, [AbstractNormalizer::ATTRIBUTES => ['id', 'blocks']]);

        return $this->successResponse($data);
    }

    /**
     * Выгружает все кастомные уровни
     * @Route("/api/v.0/level/get/all-custom", methods={"GET", "OPTIONS"})
     *
     * @return Response|null
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function getAllCustom() : Response
    {
        /** @var Level $levels */
        $levels = $this->getDoctrine()
            ->getRepository(Level::class)
            ->getAllCustom();

        if (!$levels) return $this->errorResponse('Кастомных уровней почему-то нет');

        $serializer = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);
        $data = $serializer->normalize($levels, null, [AbstractNormalizer::ATTRIBUTES => ['id', 'blocks']]);

        return $this->successResponse($data);
    }
}
