<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/user', name: 'user')]
class UserController extends BaseController
{
    #[Route(name: 'get', methods: 'POST')]
    public function index(): JsonResponse
    {
        return self::response($this->getUser(), Response::HTTP_CREATED, [], [
            'groups' => ['user']
        ]);
    }
}