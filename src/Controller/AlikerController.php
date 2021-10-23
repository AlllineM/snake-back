<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Общие методы всех контроллеров
 * Class AlikerController
 * @package App\Controller
 */
class AlikerController extends AbstractController
{
    /**
     * Общая упаковка успешных ответов апи
     * @param $result array
     * @return Response
     */
    public function successResponse($result)
    {
        $data = ['status' => 200, 'result' => $result];
        $jsonContent = json_encode($data);

        //todo: Кроссдомены
        return new Response($jsonContent, 200, ['Access-Control-Allow-Origin' => '*']);
    }

    /**
     * Общая упаковка ошибок
     * @param $errorMessage
     * @return Response
     */
    public function errorResponse($errorMessage)
    {
        $data = ['status' => 400, 'error' => $errorMessage];
        $jsonContent = json_encode($data);

        //todo: Кроссдомены
        return new Response($jsonContent, 400, ['Access-Control-Allow-Origin' => '*']);
    }

    /**
     * Если isSubmitted false
     * @return Response
     */
    public function formError()
    {
        return $this->errorResponse('Форма не отправлена');
    }
}
