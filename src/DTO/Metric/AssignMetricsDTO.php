<?php

namespace App\DTO\Metric;

class AssignMetricsDTO
{
    public function __construct(
        /**
         * @var array<AssignProtagonistMetricDTO>
         */
        public array $metrics,
    ) {}
}