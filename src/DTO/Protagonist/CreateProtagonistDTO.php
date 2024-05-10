<?php

namespace App\DTO\Protagonist;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Regex;

class CreateProtagonistDTO
{
    public function __construct(
        #[Assert\NotBlank]
        #[Regex(
            pattern: '/^[a-zA-Z0-9\- ]+$/',
            message: 'Title contains wrong characters',
            match: true

        )]
        public string $name,

        #[Assert\NotBlank]
        #[Regex(
            pattern: '/^[a-zA-Z0-9\- ]+$/',
            message: 'Slug contains wrong characters',
            match: true

        )]
        public string $slug,

        public string $story,

        public File $portraitFile,
    ) {}
}