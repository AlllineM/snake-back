<?php

namespace App\Controller;

use App\Entity\Fact;
use App\Entity\Log;
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

class LogController extends AlikerController
{
    /**
     * Создать нового незарегистрированного юзера
     * @Route("/api/v.0/log/create", methods={"POST", "OPTIONS"})
     * @param Request $request
     * @return Response
     */
    public function create(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);

        $log = new Log();
        if (!empty($data['temporaryUserId'])) {
            $utmUser = $this->getDoctrine()
                ->getRepository(UtmUser::class)
                ->findByTemporaryId($data['temporaryUserId']);
            if (!empty($utmUser)) {
                $log->setUtmUser($utmUser);
            }
        }
        $log->setAction($data['action'] ?? null);
        $log->setDateTime(new \DateTime());

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($log);
        $entityManager->flush();

        return $this->successResponse('ok');
    }
}
