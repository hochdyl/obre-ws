<?php

namespace App\DTO\Protagonist;

use App\Entity\Upload;
use Symfony\Component\Validator\Constraints as Assert;

class EditProtagonistDTO
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Length(max: 30, maxMessage: 'Maximum name size is {{ limit }} characters')]
        public string $name,

        #[Assert\NotBlank]
        public string $slug,

        public string | null $story,

        #[Assert\Range(notInRangeMessage: 'Level must be between {{ min }} and {{ max }}', min: 1, max: 9999)]
        public int $level,

        #[Assert\NotBlank]
        public Upload $portrait
    ) {}
}