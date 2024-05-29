<?php

namespace App\DTO\Protagonist;

use Symfony\Component\Validator\Constraints as Assert;

class EditProtagonistDTO
{
    public function __construct(
        #[Assert\NotBlank]
        public string $name,

        #[Assert\NotBlank]
        public string $slug,

        #[Assert\GreaterThanOrEqual(1)]
        public int $level,

        public ?string $story = null
    ) {}
}