<?php

namespace App\DTO\Metric;

use Symfony\Component\Validator\Constraints as Assert;

class AssignMetricDTO
{
    public function __construct(
        #[Assert\NotBlank]
        public int $id,

        #[Assert\NotBlank]
        public string $name,

        public string | null $emoji,
    ) {}
}