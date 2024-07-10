<?php

namespace App\DTO\Metric;

use App\Entity\Metric;
use Symfony\Component\Validator\Constraints as Assert;

class AssignProtagonistMetricDTO
{
    public function __construct(
        public int | null $id,

        #[Assert\NotBlank]
        public int $value,

        public int | null $max,

        #[Assert\NotBlank]
        public AssignMetricDTO $metricDetails
    ) {}
}