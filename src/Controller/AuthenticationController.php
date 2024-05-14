<?php

namespace App\Controller;

use App\DTO\Authentication\LoginUserDTO;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\UserPasswordService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/authentication', name: 'authentication')]
class AuthenticationController extends BaseController
{
    /**
     * Register a user
     *
     * @param User $user
     * @param EntityManagerInterface $em
     * @param UserPasswordService $userPasswordService
     * @return JsonResponse
     */
    #[Route('/register', name: 'register', methods: 'POST')]
    public function register(
        #[MapRequestPayload(
            serializationContext: [
                'groups' => ['user.register']
            ]
        )] User                $user,
        EntityManagerInterface $em,
        UserPasswordService    $userPasswordService,
    ): JsonResponse
    {
        $userPasswordService->encryptPassword($user);
        $user->generateSessionToken();

        $em->persist($user);
        $em->flush();

        return self::response($user, Response::HTTP_CREATED, [], [
            'groups' => ['user']
        ]);
    }

    /**
     * Return a user from login data
     *
     * @throws Exception
     */
    #[Route('/login', name: 'login', methods: 'POST')]
    public function login(
        #[MapRequestPayload] LoginUserDTO $loginDTO,
        EntityManagerInterface            $em,
        UserRepository                    $userRepository,
        UserPasswordService               $userPasswordService
    ): JsonResponse
    {
        $storedUser = $userRepository->findOneBy(['username' => $loginDTO->username]);
        if (!$storedUser || !$userPasswordService->isPasswordValid($storedUser, $loginDTO->password)) {
            throw new Exception("Invalid credentials", Response::HTTP_UNAUTHORIZED);
        }

        $storedUser->generateSessionToken();

        $em->persist($storedUser);
        $em->flush();

        return self::response($storedUser, Response::HTTP_OK, [], [
            'groups' => ['user']
        ]);
    }
}