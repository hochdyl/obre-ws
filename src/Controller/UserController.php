<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class UserController extends AbstractController
{
    #[Route('/user', name: 'create_user', methods: 'POST')]
    public function createUser(Request $request): JsonResponse
    {
        var_dump($request->request->all());
        return $this->json(['test' => 'test POST']);
    }

//    #[Route('/user', name: 'app_user', methods: 'GET')]
//    #[IsGranted('ROLE_USER')]
//    public function index(): JsonResponse
//    {
//        return $this->json(['test' => 'test GET']);
//    }
}