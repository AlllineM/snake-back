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

class FactController extends AlikerController
{
    /**
     * Получить рандомный факт
     * @Route("/api/v.0/fact/get-random", methods={"GET", "OPTIONS"})
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

    /**
     * Получить первые 20 штук, для незареганных юзеров
     * @Route("/api/v.0/fact/get/first", methods={"GET", "OPTIONS"})
     *
     * @return Response|null
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function getFirst() : Response
    {
        /** @var Fact $fact */
        $fact = $this->getDoctrine()
            ->getRepository(Fact::class)
            ->getFirst();

        if (!$fact) return $this->successResponse(
            ['message' => 'Странная ошибка, что-то не так с базой'],
            404
        );

        $serializer = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);
        $data = $serializer->normalize($fact, null, [AbstractNormalizer::ATTRIBUTES => ['id', 'body']]);

        return $this->successResponse($data);
    }


    /**
     * Добавить новый факт
     * @Route("/api/v.0/fact/add", methods={"POST", "OPTIONS"})
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return Response
     * @throws \Exception
     */
    public function add(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
       $data = json_decode($request->getContent(), true);

        $fact = new Fact();
        $form = $this->createForm(AddFactFormType::class, $fact);
        $form->submit($data);

        //todo: добавить валидацию
        if ($form->isSubmitted()) {
            $user = $this->getDoctrine()
                ->getRepository(UserAliker::class)
                ->findOneById($this->getUser()->getId());

            $fact->setAuthor($user);
            $fact->setDateTime(new \DateTime());
            $fact->setStatus(Fact::NEW);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($fact);
            $entityManager->flush();

            return $this->successResponse(['factId' => $user->getId()]);
        } else  {
            return $this->formError();
        }
    }

    /**
     * Получить все факты, которые написал юзер
     * @Route("/api/v.0/fact/get-all", methods={"GET", "OPTIONS"})
     *
     * @return Response|null
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function getAll() : Response
    {
        /** @var Fact $fact */
        $facts = $this->getDoctrine()
            ->getRepository(Fact::class)
            ->getAllByAuthor($this->getUser()->getId());

        if (!$facts) return $this->successResponse(
            ['message' => 'Вы еще не написали ни одного факта о себе'],
            404
        );

        $serializer = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);
        $data = $serializer->normalize($facts, null, [AbstractNormalizer::ATTRIBUTES => ['id', 'body']]);

        return $this->successResponse($data);
    }
}
