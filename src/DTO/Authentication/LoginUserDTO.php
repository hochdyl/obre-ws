<?php

namespace App\DTO\Authentication;

use Symfony\Component\Validator\Constraints as Assert;

class LoginUserDTO
{
    public function __construct(
        #[Assert\NotBlank]
        public string $username,

        #[Assert\NotBlank]
        public string $password,
    ) {}
}