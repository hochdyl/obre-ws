<?php

namespace App\DTO\Game;

use DateTimeImmutable;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Regex;

class EditGameDTO
{
    public function __construct(
        #[Assert\NotBlank]
        public string $title,

        #[Assert\NotBlank]
        #[Regex(
            pattern: '/^[a-zA-Z0-9\- ]+$/',
            message: 'Slug is invalid',
            match: true
        )]
        public string $slug,

        #[Assert\NotBlank]
        public ?DateTimeImmutable $startedAt = new DateTimeImmutable(),

        public ?bool $closed = false,
    ) {}
}