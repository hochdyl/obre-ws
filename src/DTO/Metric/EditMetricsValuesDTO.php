<?php

namespace App\DTO\Metric;

use Symfony\Component\Validator\Constraints as Assert;

class EditMetricsValuesDTO
{
    public function __construct(

        /**
         * @var array<MetricDTO>
         */
        #[Assert\NotBlank]
        public array $metricsValues,
    ) {}
}