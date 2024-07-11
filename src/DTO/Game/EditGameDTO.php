<?php

namespace App\DTO\Game;

use DateTimeImmutable;
use Symfony\Component\Validator\Constraints as Assert;

class EditGameDTO
{
    public function __construct(
        #[Assert\NotBlank]
        public string $title,

        #[Assert\NotBlank]
        public string $slug,

        #[Assert\NotBlank]
        public ?DateTimeImmutable $startedAt = new DateTimeImmutable(),

        public ?bool $closed = false,
    ) {}
}