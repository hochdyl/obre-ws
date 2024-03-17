<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

class UserController extends AbstractController
{
    #[Route('/register', name: 'register_user', methods: 'POST')]
//    #[IsGranted('ROLE_USER')]
    public function registerUser(
        #[MapRequestPayload(
            serializationContext: [
                'groups' => ['user.register']
            ]
        )] User $user,
        EntityManagerInterface $em,
        UserService $userService
    ): JsonResponse
    {
        $userService->encryptPassword($user);
        $userService->generateApiToken($user);

        $em->persist($user);
        $em->flush();

        return $this->json($user, 200, [], [
            'groups' => ['user.token']
        ]);
    }
}