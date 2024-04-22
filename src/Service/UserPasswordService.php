<?php

namespace App\Service;

use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Uid\Uuid;

readonly class UserPasswordService
{
    public function __construct(private UserPasswordHasherInterface $passwordHasher) {}

    public function encryptPassword(User $user): User
    {
        $plainPassword = $user->getPassword();
        $hashedPassword = $this->passwordHasher->hashPassword($user, $plainPassword);
        return $user->setPassword($hashedPassword);
    }

    public function isPasswordValid(User $user, string $plainPassword): bool {
         return $this->passwordHasher->isPasswordValid($user, $plainPassword);
    }
}