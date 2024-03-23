<?php

namespace App\Service;

use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Uid\Uuid;

readonly class UserService
{
    public function __construct(private UserPasswordHasherInterface $passwordHasher) {}

    public function encryptPassword(User $user): User
    {
        $plaintextPassword = $user->getPassword();
        $hashedPassword = $this->passwordHasher->hashPassword($user, $plaintextPassword);
        return $user->setPassword($hashedPassword);
    }

    public function generateApiToken(User $user): User
    {
        return $user->setApiToken(Uuid::v1());
    }
}