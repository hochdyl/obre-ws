<?php

namespace App\DTO\Metric;

use App\Entity\Metric;
use Symfony\Component\Validator\Constraints as Assert;

class MetricValueDTO
{
    public function __construct(
        public int | null $id,

        public string | null $emoji,

        #[Assert\NotBlank]
        public string $name,

        #[Assert\NotBlank]
        public int $value,

        public int | null $max
    ) {}
}