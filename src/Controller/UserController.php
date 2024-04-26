<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/users', name: 'users')]
class UserController extends BaseController
{
    /**
     * Return user from current session
     *
     * @return JsonResponse
     */
    #[Route(name: 'self', methods: 'GET')]
    public function self(): JsonResponse
    {
        return self::response($this->getUser(), Response::HTTP_OK, [], [
            'groups' => ['user']
        ]);
    }
}
