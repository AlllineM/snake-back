<?php

namespace App\Controller;

use App\Entity\Fact;
use App\Entity\Level;
use App\Entity\UserRate;
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

//todo: Жуть полная, рефакторить все))
class RateController extends AlikerController
{
    /**
     * Выгружает все базовые уровни
     * @Route("/api/v.0/rate/get/random", methods={"POST", "OPTIONS"})
     *
     * @return Response|null
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function getRandomPhoto(Request $request) : Response
    {
        $params = json_decode($request->getContent(), true);
        $levelId = $params['level'];

        /** @var Level $level */
        $level = $this->getDoctrine()
            ->getRepository(Level::class)
            ->getOne($levelId);

        if (!$level) return $this->errorResponse('Уровень не найден');

        $serializer = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);
        $data = $serializer->normalize($level, null, [AbstractNormalizer::ATTRIBUTES => ['id', 'blocks']]);

        return $this->successResponse($data[0]);
    }



    /**
     * Добавить фото
     * @Route("/api/v.0/photo/add", methods={"POST", "OPTIONS"})
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function addPhoto(Request $request): Response
    {
       // await fetch("http://localhost:7777/api/v.0/photo/add", {
//        await fetch("https://test-constructor.ru/api/v.0/photo/add", {
//            "credentials": "omit",
//            "headers": {
//                "User-Agent": "Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:109.0) Gecko/20100101 Firefox/119.0",
//                "Accept": "text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8",
//                "Accept-Language": "ru-RU,ru;q=0.8,en-US;q=0.5,en;q=0.3",
//                "Upgrade-Insecure-Requests": "1",
//                "Sec-Fetch-Dest": "document",
//                "Sec-Fetch-Mode": "navigate",
//                "Sec-Fetch-Site": "none",
//                "Sec-Fetch-User": "?1"
//            },
//            "method": "POST",
//            "mode": "cors",
//            "body": JSON.stringify({user_token:'3', type:'draft', blocks: [{x:1, y:2}]})
//        });

//        $params = json_decode($request->getContent(), true);
//        $userToken = $params['user_token'] ?? null;
//        $blocks = $params['blocks'] ?? null;
//
//        /** @var UserRate $user */
//        $user = $this->getDoctrine()
//            ->getRepository(UserRate::class)
//            ->getOne($userToken);
//        if (!$user) {
//            $user = new UserRate();
//            $user->setToken($userToken);
//        }
//
//        $entityManager = $this->getDoctrine()->getManager();
//        $entityManager->persist($user);
//        $entityManager->flush();

        return $this->successResponse(['ok' => 'ok']);
    }

    /**
     * Создать уровень
     * @Route("/api/v.0/level/edit", methods={"POST", "OPTIONS"})
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function edit(Request $request): Response
    {
        // await fetch("http://localhost:7777/api/v.0/level/create", {
//        await fetch("https://test-constructor.ru/api/v.0/level/create", {
//            "credentials": "omit",
//            "headers": {
//                "User-Agent": "Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:109.0) Gecko/20100101 Firefox/119.0",
//                "Accept": "text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8",
//                "Accept-Language": "ru-RU,ru;q=0.8,en-US;q=0.5,en;q=0.3",
//                "Upgrade-Insecure-Requests": "1",
//                "Sec-Fetch-Dest": "document",
//                "Sec-Fetch-Mode": "navigate",
//                "Sec-Fetch-Site": "none",
//                "Sec-Fetch-User": "?1"
//            },
//            "method": "POST",
//            "mode": "cors",
//            "body": JSON.stringify({user_token:'3', type:'draft', blocks: [{x:1, y:2}]})
//        });

        $params = json_decode($request->getContent(), true);
        $levelId = $params['level_id'] ?? null;

        /** @var Level $level */
        $level = $this->getDoctrine()
            ->getRepository(Level::class)
            ->getOne($levelId);
        if (!$level) {
            return $this->errorResponse('Уровень не найден');
        }

        $userToken = $params['user_token'] ?? null;
        if ($level->getUserToken() !== $userToken) {
            return $this->errorResponse('Ошибка доступа. Юзер не является автором уровня');
        }

        $startSpeed = $params['start_speed'] ?? null;
        $boostSpeed = $params['boost_speed'] ?? null;
        $blocks = $params['blocks'] ?? null;
        $type = $params['type'] ?? null;

        $level->setBlocks($blocks);
        $level->setStartSpeed($startSpeed);
        $level->setBoostSpeed($boostSpeed);
        if ($type == 'publish') {
            $level->setStatus(Level::STATUS_MODERATION);
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($level);
        $entityManager->flush();

        return $this->successResponse(['levelId' => $level->getId()]);
    }

    /**
     * Выгружает все базовые уровни
     * @Route("/api/v.0/level/get/one", methods={"POST", "OPTIONS"})
     *
     * @return Response|null
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function getOne(Request $request) : Response
    {
        $params = json_decode($request->getContent(), true);
        $levelId = $params['level'];

        /** @var Level $level */
        $level = $this->getDoctrine()
            ->getRepository(Level::class)
            ->getOne($levelId);

        if (!$level) return $this->errorResponse('Уровень не найден');

        $serializer = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);
        $data = $serializer->normalize($level, null, [AbstractNormalizer::ATTRIBUTES => ['id', 'blocks']]);

        return $this->successResponse($data[0]);
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

    /**
     * Возвращает актуальную версию андроид-приложения
     * todo: Потом вынести в другой контроллер и убрать захардкоденное
     *
     * @Route("/api/v.0/version/get", methods={"GET", "OPTIONS"})
     * @return Response
     */
    public function getCurrentVersion() : Response
    {
        return $this->successResponse(['currentVersion' => '0.1']);
    }
}
