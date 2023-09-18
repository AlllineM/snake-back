<?php

namespace App\Controller;

use App\Entity\Fact;
use App\Entity\UtmUser;
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

class UtmUserController extends AlikerController
{
    /**
     * Создать нового незарегистрированного юзера
     * @Route("/api/v.0/utmUser/create", methods={"POST", "OPTIONS"})
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return Response
     * @throws \Exception
     */
    public function add(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $data = json_decode($request->getContent(), true);

        $utmUser = new UtmUser();

        $fact->setAuthor($user);
        $fact->setDateTime(new \DateTime());
        $fact->setStatus(Fact::NEW);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($fact);
        $entityManager->flush();

        return $this->successResponse(['factId' => $user->getId()]);

    }
}
