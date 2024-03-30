<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

class AuthenticationController extends BaseController
{
    #[Route('/self', name: 'self', methods: 'GET')]
    public function self(): JsonResponse
    {
        return self::response($this->getUser(), Response::HTTP_OK, [], [
            'groups' => ['user']
        ]);
    }

    #[Route('/register', name: 'register', methods: 'POST')]
    public function registerUser(
        #[MapRequestPayload(
            serializationContext: [
                'groups' => ['user.register']
            ]
        )] User $user,
        EntityManagerInterface $em,
        UserService $userService,
    ): JsonResponse
    {
        $userService->encryptPassword($user);
        $userService->generateApiToken($user);

        $em->persist($user);
        $em->flush();

        return self::response($user, Response::HTTP_CREATED, [], [
            'groups' => ['user']
        ]);
    }
}