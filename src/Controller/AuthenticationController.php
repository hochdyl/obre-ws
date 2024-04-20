<?php

namespace App\Controller;

use App\Dto\Authentication\LoginDTO;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

class AuthenticationController extends BaseController
{
    /**
     * Return a user from current session
     *
     * @return JsonResponse
     */
    #[Route('/self', name: 'self', methods: 'GET')]
    public function self(): JsonResponse
    {
        return self::response($this->getUser(), Response::HTTP_OK, [], [
            'groups' => ['user']
        ]);
    }

    /**
     * Register a new user
     *
     * @param User $user
     * @param EntityManagerInterface $em
     * @param UserService $userService
     * @return JsonResponse
     */
    #[Route('/register', name: 'register', methods: 'POST')]
    public function register(
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

    /**
     * Return a user from login form data
     *
     * @throws Exception
     */
    #[Route('/login', name: 'login', methods: 'POST')]
    public function login(
        #[MapRequestPayload] LoginDTO $loginDTO,
        EntityManagerInterface $em,
        UserRepository $userRepository,
        UserService $userService
    ): JsonResponse
    {
        $storedUser = $userRepository->findOneBy(['username' => $loginDTO->username]);

        if(!$storedUser || !$userService->isPasswordValid($storedUser, $loginDTO->password)) {
            throw new Exception("Invalid credentials", Response::HTTP_UNAUTHORIZED);
        }

        $userService->generateApiToken($storedUser);

        $em->persist($storedUser);
        $em->flush();

        return self::response($storedUser, Response::HTTP_OK, [], [
            'groups' => ['user']
        ]);
    }
}