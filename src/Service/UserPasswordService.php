<?php

namespace App\Service;

use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

readonly class UserPasswordService
{
    public function __construct(private UserPasswordHasherInterface $passwordHasher)
    {}

    /**
     * Hash and set user password
     *
     * @param User $user
     * @return User
     */
    public function hashPassword(User $user): User
    {
        $plainPassword = $user->getPassword();
        $hashedPassword = $this->passwordHasher->hashPassword($user, $plainPassword);
        return $user->setPassword($hashedPassword);
    }

    /**
     * Verify if password is valid
     *
     * @param User $user
     * @param string $plainPassword
     * @return bool
     */
    public function isPasswordValid(User $user, string $plainPassword): bool {
         return $this->passwordHasher->isPasswordValid($user, $plainPassword);
    }
}