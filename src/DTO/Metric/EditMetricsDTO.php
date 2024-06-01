<?php

namespace App\DTO\Metric;

use Symfony\Component\Validator\Constraints as Assert;

class EditMetricsDTO
{
    public function __construct(

        /**
         * @var array<MetricDTO>
         */
        #[Assert\NotBlank]
        public array $metrics,
    ) {}
}