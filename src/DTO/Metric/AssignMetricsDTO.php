<?php

namespace App\DTO\Metric;

use Symfony\Component\Validator\Constraints as Assert;

class AssignMetricsDTO
{
    public function __construct(

        /**
         * @var array<MetricValueDTO>
         */
        #[Assert\NotBlank]
        public array $metricsValues,
    ) {}
}