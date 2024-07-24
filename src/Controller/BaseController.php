<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

abstract class BaseController extends AbstractController
{
    /*
     * JSend format response
     *
     * @see https://github.com/omniti-labs/jsend
     */
    protected function response(
        mixed $data,
        int   $status = Response::HTTP_OK,
        array $headers = [],
        array $context = []
    ): JsonResponse
    {
        $statusName = "success";
        if ($status < 200 || $status >= 300) {
            $statusName = gettype($data) === "string" ? "error" : "fail";
        }

        return $this->json([
            'status' => $statusName,
            'data' => $data
        ], $status, $headers, $context);
    }
}
