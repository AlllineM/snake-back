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
     * Создать уровень
     * @Route("/api/v.0/level/create", methods={"POST", "OPTIONS"})
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function create(Request $request): Response
    {
        //await fetch("http://localhost:7777/api/v.0/level/create", {
        //    "credentials": "omit",
        //    "headers": {
        //        "User-Agent": "Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:109.0) Gecko/20100101 Firefox/119.0",
        //        "Accept": "text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8",
        //        "Accept-Language": "ru-RU,ru;q=0.8,en-US;q=0.5,en;q=0.3",
        //        "Upgrade-Insecure-Requests": "1",
        //        "Sec-Fetch-Dest": "document",
        //        "Sec-Fetch-Mode": "navigate",
        //        "Sec-Fetch-Site": "none",
        //        "Sec-Fetch-User": "?1"
        //    },
        //    "method": "POST",
        //    "mode": "cors",
        //    "body": JSON.stringify({user_token:'3', blocks: [{x:1, y:2}]})
        //});

        $params = json_decode($request->getContent(), true);
        $userToken = $params['user_token'];
        $blocks = $params['blocks'];

        $level = new Level();
        $level->setUserToken($userToken);
        $level->setBlocks($blocks);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($level);
        $entityManager->flush();

        return $this->successResponse(['levelId' => $level->getId()]);
    }

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
