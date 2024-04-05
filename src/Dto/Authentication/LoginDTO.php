<?php

namespace App\Dto\Authentication;

use Symfony\Component\Validator\Constraints as Assert;

class LoginDTO
{
    public function __construct(
        #[Assert\NotBlank]
        public string $username,
        #[Assert\NotBlank]
        public string $password,
    ) {}
}