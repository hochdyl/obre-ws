<?php

namespace App\DTO\Protagonist;

use App\Entity\Upload;
use Symfony\Component\Validator\Constraints as Assert;

class EditProtagonistDTO
{
    public function __construct(
        #[Assert\NotBlank]
        public string $name,

        #[Assert\NotBlank]
        public string $slug,

        public string | null $story,

        #[Assert\GreaterThanOrEqual(1)]
        public int $level,

        #[Assert\NotBlank]
        public Upload $portrait
    ) {}
}