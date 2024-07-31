<?php

namespace App\Controller;

use App\Entity\Fact;
use App\Entity\Level;
use App\Entity\Reaction;
use App\Entity\UserRate;
use App\Form\AddFactFormType;
use App\Helpers\Logger;
use App\Repository\ReactionRepository;
use App\Services\ReactionFactory;
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
     * Добавить оценку факта
     * @Route("/api/v.0/rate/add", methods={"OPTIONS", "POST"})
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
     * Добавить фото
     * @Route("/api/v.0/photo/add", methods={"POST", "OPTIONS"})
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function addPhoto(Request $request,  Logger $logger): Response
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

        $requestData = $request->getContent();
        //var_dump($requestData);
        //$logger->errorTelegram($requestData);

        $params = json_decode($requestData, true);

        $userToken = $params['userToken'] ?? null;
        $logger->errorTelegram($userToken);

        if (!empty($userToken)) {
            $logger->errorTelegram("1");
            $entityManager = $this->getDoctrine()->getManager();

            $logger->errorTelegram("2");

            /** @var $userRate UserRate */
            $userRate = $this->getDoctrine()
                ->getRepository(UserRate::class)
                ->getOne($userToken);
            $logger->errorTelegram("3");

            if (empty($userRate)) {
                $userRate = new UserRate();
                $userRate->setToken($userToken);
                $userRate->setRegDateTime((new \DateTime()));
                $logger->errorTelegram("4");

                //сначала сохраняем для айдишника. Потом генерим ссылку на картинку
                $entityManager->persist($userRate);
                $entityManager->flush();
                $logger->errorTelegram("5");

            }
            $logger->errorTelegram("6");


            $logger->errorTelegram($userRate->generateImageName());
            //сохраняем ссылку
            $userRate->setPhotoLink($userRate->generateImageName());
            $entityManager->persist($userRate);
            $entityManager->flush();
        }

        if (!empty($userToken) && !empty($userRate)) {
            $imageName = $userRate->getPhotoLink();
        } else {
            //пусть пока просто сохраняет, потом посмотрим
            $imageName = UserRate::generateAnonImageName();
        }

        $logger->errorTelegram($imageName);

        $imageString = $params['imageString'] ?? null;
        //var_dump($imageString);
        //$logger->errorTelegram($imageString);

        $jpeg = base64_decode($imageString);
        $coolerImagesPath = __DIR__ . '/../../../../cooler/images/';
        file_put_contents($coolerImagesPath . $imageName . '.jpeg', $jpeg);


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
