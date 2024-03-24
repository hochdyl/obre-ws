<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

abstract class BaseController extends AbstractController
{
    /*
     * JSend standard response
     *
     * @see https://github.com/omniti-labs/jsend
     */
    protected function response(
        mixed $data,
        int $status = Response::HTTP_OK,
        array $headers = [],
        array $context = []
    ): JsonResponse
    {
        return $this->json([
            'status' => 'success',
            'data' => $data
        ], $status, $headers, $context);
    }
}
