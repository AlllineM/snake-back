<?php

namespace App\Controller;

use App\Entity\UtmUser;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class UtmUserController extends AlikerController
{
    /**
     * Создать нового незарегистрированного юзера
     * @Route("/api/v.0/utmUser/create", methods={"POST", "OPTIONS"})
     * @param Request $request
     * @return Response
     */
    public function create(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);

        $utmUser = new UtmUser();
        $utmUser->setTemporaryId($data['temporaryUserId']);
        $utmUser->setUtmSource($data['utmSource']);
        $utmUser->setUtmMedium($data['utmMedium']);
        $utmUser->setCreateDateTime(new \DateTime());

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($utmUser);
        $entityManager->flush();

        return $this->successResponse('ok');
    }
}
