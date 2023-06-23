<?php

namespace App\Controller;

use App\Entity\Fact;
use App\Entity\Reaction;
use App\Entity\TelegramUser;
use App\Helpers\Logger;
use App\Repository\ReactionRepository;
use App\Services\ReactionFactory;
use App\Services\TelegramService;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\UserAliker;
use App\Form\RegistrationFormType;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;


class UserAlikerController extends AlikerController
{
    /**
     * Регистрация
     * @Route("/api/v.0/user/add", methods={"OPTIONS", "POST"})
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return Response
     * @throws \Exception
     */
    //todo: Возможно, прибить кириллицу в никах. Это не тут, но тем не менее)
    public function register(
        Request $request,
        UserPasswordEncoderInterface $passwordEncoder,
        ReactionFactory $reactionFactory
    ): Response
    {
        $data = json_decode($request->getContent(), true);

        $user = new UserAliker();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->submit($data);

        //todo: добавить валидацию
        if ($form->isSubmitted()) {
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $user->setRating(0);
            $user->setBalance(5000);
            $user->setRoles(['ROLE_USER']);

            $token = bin2hex(random_bytes(15));
            $user->setApiToken($token);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            //todo: вынести это куда-нибудь
            foreach ($data['firstFacts'] as $firstFactsDatum) {
                if (empty($firstFactsDatum['status'])) {
                    continue;
                }
                $reaction = $reactionFactory->makeReaction(
                    $user,
                    $firstFactsDatum['id'],
                    $firstFactsDatum['status']
                );
                $entityManager->persist($reaction);
            }
            $entityManager->flush();

            $data = ['user' => $user->getId(), 'token' => $token];

            return $this->successResponse($data);

        } else {
            return $this->formError();
        }
    }

    /**
     * Тестовая штука
     * @Route("/api/v.0/user/telegramlogin", methods={"GET", "OPTIONS"})
     **/
    public function telegramLoginTest() : Response
    {
        return $this->render('base.html.twig');
    }

    /**
     * Вход
     * @Route("/api/v.0/user/login")
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return Response
     * @throws \Exception
     */
    public function login(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $data = json_decode($request->getContent(), true);

        /** @var $user UserAliker */
        $user = $this->getDoctrine()
            ->getRepository(UserAliker::class)
            ->findOneByEmail($data['email']);

        if (empty($user)) {
            return $this->errorResponse('Пользователь с таким email не найден');
        }

        if ($passwordEncoder->isPasswordValid($user, $data['password'])) {
            $token = bin2hex(random_bytes(15));
            $user->setApiToken($token);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $data = ['token' => $token];

            return $this->successResponse($data);
        } else {
            return $this->errorResponse('Неверный пароль');
        }
    }

    /**
     * @Route("/api/v.0/user/telegram/login", methods={"OPTIONS", "GET"})
     * @param Request $request
     * @param Logger $logger
     * @return Response
     */
    public function telegramLogin(Request $request, TelegramService $telegramService, Logger $logger)
    {
        $data = $request->query->all();
        $hash = $data['hash'];
        $logger->emptyMessageTelegram($data);
        $response = $this->redirect('https://aliker.ru/telegram-login');

        try {
            $telegramService->checkTelegramAuthorization($data);
        } catch (\Exception $exception) {
            $logger->errorTelegram($exception->getMessage());
            //если хэш не прошел, редиректит без куки
            return $response;
        }

        /** @var $telegramUser TelegramUser */
        $telegramUser = $this->getDoctrine()
            ->getRepository(TelegramUser::class)
            ->findOneById($data['id']);
        $entityManager = $this->getDoctrine()->getManager();

        if (empty($telegramUser)) {
            $telegramUser = new TelegramUser();
            $telegramUser->setId($data['id']);
            $telegramUser->setUsername($data['username'] ?? '');
            $telegramUser->setFirstName($data['first_name'] ?? '');
            $telegramUser->setLastName($data['last_name'] ?? '');
            $entityManager->persist($telegramUser);
            $entityManager->flush();

            $user = $this->getUser();
            if (empty($user)) {
                //если юзер совсем новый, создаем
                $user = new UserAliker();
                $user->setRating(0);
                $user->setBalance(5000);
                $user->setRoles(['ROLE_USER']);
            }
            //если юзер уже зареган и просто добавляет телегу, только меняем хэш
            $user->setApiToken($hash);
            $user->setTelegramUser($telegramUser);
            $entityManager->persist($user);
            $entityManager->flush();
        } else {
            //вход
            $user = $telegramUser->getUserAliker();
            $user->setApiToken($hash);
            $entityManager->persist($user);
            $entityManager->flush();
        }

        $response = $this->redirect('https://aliker.ru/telegram-login');
        $response->headers->setCookie(new Cookie('telegram_token', $hash));

        return $response;
    }

    /**
     * Получить список мэйтов
     * @Route("/api/v.0/user/get/loguot", methods={"OPTIONS", "GET"})
     */
    public function logout()
    {
        $user = $this->getUser();
        $user->setApiToken(null);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        return $this->successResponse(['status' => 'ok']);
    }

    /**
     * Получить список мэйтов
     * @Route("/api/v.0/user/get/mates", methods={"OPTIONS", "GET"})
     */
    public function getMates()
    {
        $userId = $this->getUser()->getId();
        $doctrine = $this->getDoctrine();

        $likes = $doctrine->getRepository(Reaction::class)
            ->findLikes($userId);

        if (!$likes) return $this->errorResponse('Юзер еще не лайкнул ни одного факта');

        $mates = $doctrine->getRepository(Reaction::class)
            ->getMates($likes, $userId);

        return $this->successResponse($mates);
    }

    /**
     * Список мэйтов по новому алгоритму
     * @Route("/api/v.0/user/get/new-mates", methods={"OPTIONS", "GET"})
     * @return Response
     */
    public function getNewMates()
    {
        $userId = $this->getUser()->getId();
        $doctrine = $this->getDoctrine();

        $result = $doctrine->getRepository(Reaction::class)
            ->getNewMates($userId);


        return $this->successResponse($result);
    }

    /**
     * Возвращает инфу о текущем юзере
     * @Route("/api/v.0/user/get/profile-info", methods={"OPTIONS", "GET"})
     * @return Response
     */
    public function getProfileInfo(Request $request)
    {
       $result['email'] = $this->getUser()->getEmail();
       $result['id'] = $this->getUser()->getId();

       return $this->successResponse($result);
    }

    /**
     * Возвращает баланс текущего(авторизованного) юзера
     * @Route("/api/v.0/user/get/balance", methods={"OPTIONS", "GET"})
     * @return Response
     */
    public function getBalance(Request $request)
    {
       return $this->successResponse($this->getUser()->getBalance());
    }

    /**
     * Возвращает рейтинг текущего юзера
     * @Route("/api/v.0/user/get/rating", methods={"OPTIONS", "GET"})
     * @return Response
     */
    public function getRating()
    {
        return $this->successResponse($this->getUser()->getRating());
    }
}
