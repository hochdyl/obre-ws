<?php

namespace App\DTO\Metric;

use Symfony\Component\Validator\Constraints as Assert;

class EditMetricDTO
{
    public function __construct(
        #[Assert\NotBlank]
        public string $name,

        public string | null $emoji,
    ) {}
}