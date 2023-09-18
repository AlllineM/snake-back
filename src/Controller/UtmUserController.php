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
        $utmUser->setTemporaryId($data['temporaryUserId'] ?? null);
        $utmUser->setUtmSource($data['utmSource'] ?? null);
        $utmUser->setUtmMedium($data['utmMedium'] ?? null);
        $utmUser->setUtmCampaign($data['utmCampaign'] ?? null);
        $utmUser->setUtmTerm($data['utmTerm'] ?? null);
        $utmUser->setUtmContent($data['utmContent'] ?? null);
        $utmUser->setCreateDateTime(new \DateTime());

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($utmUser);
        $entityManager->flush();

        return $this->successResponse('ok');
    }
}
