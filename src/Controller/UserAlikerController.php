<?php

namespace App\Controller;

use App\Entity\Fact;
use App\Entity\Reaction;
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
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
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

            $data = ['user' => $user->getId(), 'token' => $token];

            return $this->successResponse($data);

        } else {
            return $this->formError();
        }
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

        $friends = $doctrine->getRepository(Reaction::class)
            ->getFriends($userId);

        $mates = $doctrine->getRepository(Reaction::class)
            ->getMates($likes, $userId, $friends);

        return $this->successResponse($mates);
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
