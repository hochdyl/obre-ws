<?php

namespace App\DTO\Protagonist;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Regex;

class EditProtagonistDTO
{
    public function __construct(
        #[Assert\NotBlank]
        #[Regex(
            pattern: '/^(?!edit$)[a-zA-Z0-9\- ]+$/',
            message: 'Name is invalid',
            match: true

        )]
        public string $name,

        #[Assert\NotBlank]
        #[Regex(
            pattern: '/^(?!edit$)[a-zA-Z0-9\- ]+$/',
            message: 'Slug is invalid',
            match: true

        )]
        public string $slug,

        public ?string $story = null,
    ) {}
}