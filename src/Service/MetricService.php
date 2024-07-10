<?php

namespace App\Service;

use App\DTO\Metric\AssignProtagonistMetricDTO;
use App\Entity\Game;
use App\Entity\Metric;
use App\Entity\Protagonist;
use App\Entity\ProtagonistMetric;
use App\Exceptions\ObreatlasExceptions;
use App\Repository\MetricRepository;
use App\Repository\ProtagonistMetricRepository;
use Exception;

readonly class MetricService
{
    public function __construct(
        private MetricRepository $metricRepository,
        private ProtagonistMetricRepository $protagonistMetricRepository,
    )
    {}

    /**
     * Return a metric and a protagonistMetric (create a new object if needed)
     *
     * @return array{metric: Metric, protagonistMetric: ProtagonistMetric}
     * @throws Exception
     */
    function getData(AssignProtagonistMetricDTO $metricDTO, Protagonist $protagonist): array
    {
        $metricId = $metricDTO->id;
        $game = $protagonist->getGame();

        // Existing metric
        if ($metricId) {
            $metric = $this->findMetric($metricId);

            $this->updateMetric($metric, $metricDTO, $game);

            $protagonistMetric = $this->protagonistMetricRepository->findOneBy([
                'protagonist' => $protagonist->getId(),
                'metric' => $metric->getId()
            ]);

            // Existing metric on protagonist
            if ($protagonistMetric) {
                $this->updateProtagonistMetric($protagonistMetric, $protagonist, $metric, $metricDTO);

                return ['metric' => $metric, 'protagonistMetric' => $protagonistMetric];
            }

            $protagonistMetric = new ProtagonistMetric();
            $this->updateProtagonistMetric($protagonistMetric, $protagonist, $metric, $metricDTO);

            return ['metric' => $metric, 'protagonistMetric' => $protagonistMetric];
        }

        // Otherwise create everything
        $metric = new Metric();
        $this->updateMetric($metric, $metricDTO, $game);

        $protagonistMetric = new ProtagonistMetric();
        $this->updateProtagonistMetric($protagonistMetric, $protagonist, $metric, $metricDTO);

        return ['metric' => $metric, 'protagonistMetric' => $protagonistMetric];
    }

    /** @throws Exception */
    private function findMetric($metricId): Metric {
        $metric = $this->metricRepository->find($metricId);

        if (!$metric) {
            throw new Exception(ObreatlasExceptions::METRIC_NOT_FOUND);
        }

        return $metric;
    }

    private function updateProtagonistMetric(
        ProtagonistMetric $protagonistMetric,
        Protagonist $protagonist,
        Metric $metric,
        AssignProtagonistMetricDTO $metricDTO
    ): void
    {
        $protagonistMetric
            ->setProtagonist($protagonist)
            ->setMetric($metric)
            ->setValue($metricDTO->value)
            ->setMax($metricDTO->max);
    }

    private function updateMetric(Metric $metric, AssignProtagonistMetricDTO $metricDTO, Game $game): void
    {
        $metric
            ->setName($metricDTO->name)
            ->setEmoji($metricDTO->emoji)
            ->setGame($game);
    }
}