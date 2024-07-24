<?php

namespace App\DTO\Protagonist;

use App\Entity\Upload;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Regex;

class EditProtagonistDTO
{
    public function __construct(
        #[Assert\NotBlank]
        public string $name,

        #[Assert\NotBlank]
        #[Regex(
            pattern: '/^[a-zA-Z0-9\- ]+$/',
            message: 'Slug is invalid',
            match: true
        )]
        public string $slug,

        public string | null $story,

        #[Assert\GreaterThanOrEqual(1)]
        public int $level,

        #[Assert\NotBlank]
        public Upload $portrait
    ) {}
}