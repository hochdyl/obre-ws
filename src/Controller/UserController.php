<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class UserController extends AbstractController
{
    #[Route('/user', name: 'create_user', methods: 'POST')]
//    #[IsGranted('ROLE_USER')]
    public function createUser(Request $request): JsonResponse
    {
        return $this->json(['apiToken' => 'my_token_abc']);
    }
}