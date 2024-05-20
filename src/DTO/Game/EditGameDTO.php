<?php

namespace App\DTO\Game;

use DateTimeImmutable;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Regex;

class EditGameDTO
{
    public function __construct(
        #[Assert\NotBlank]
        #[Regex(
            pattern: '/^[a-zA-Z0-9\- ]+$/',
            message: 'Title contains wrong characters',
            match: true

        )]
        public string $title,

        #[Assert\NotBlank]
        #[Regex(
            pattern: '/^[a-zA-Z0-9\- ]+$/',
            message: 'Slug contains wrong characters',
            match: true

        )]
        public string $slug,

        #[Assert\NotBlank]
        public ?DateTimeImmutable $startedAt = new DateTimeImmutable(),

        public ?bool $closed = false
    ) {}
}