<?php

namespace App\Controller\crm;

use App\Controller\AlikerController;
use App\Entity\Fact;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class FactController extends AlikerController
{
    /**
     * Все факты, еще не проходившие модерацию
     * @Route("/api/v.0/crm/fact/get/new", methods={"GET", "OPTIONS"})
     *
     * @return Response|null
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function getAll() : Response
    {
        /** @var Fact $fact */
        $facts = $this->getDoctrine()
            ->getRepository(Fact::class)
            ->getAllNew();

        if (!$facts) return $this->successResponse(
            ['message' => 'Новых фактов нет'],
            404
        );

        $serializer = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);
        $data = $serializer->normalize($facts, null, [AbstractNormalizer::ATTRIBUTES => ['id', 'body']]);

        return $this->successResponse($data);
    }
}
