<?php

namespace App\Controller;

use App\DTO\Metric\EditMetricDTO;
use App\DTO\Metric\AssignMetricsDTO;
use App\Entity\Game;
use App\Entity\Metric;
use App\Entity\Protagonist;
use App\Entity\ProtagonistMetric;
use App\Exceptions\ObreatlasExceptions;
use App\Repository\MetricRepository;
use App\Repository\ProtagonistMetricRepository;
use App\Security\Voter\GameVoter;
use App\Security\Voter\ProtagonistVoter;
use App\Service\MetricService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/metrics', name: 'metrics')]
class MetricController extends BaseController
{
    #[Route('/{gameSlug}', name: 'getAll', methods: 'GET')]
    #[IsGranted(GameVoter::VIEW, subject: 'game', message: ObreatlasExceptions::CANT_VIEW_GAME)]
    #[IsGranted(GameVoter::GAME_MASTER, subject: 'game', message: ObreatlasExceptions::NOT_GAME_MASTER)]
    public function getAll(
        #[MapEntity(mapping: ['gameSlug' => 'slug'])]
        Game $game,
    ): JsonResponse
    {
        $metrics = $game->getMetrics();

        return self::response($metrics, Response::HTTP_OK, [], [
            'groups' => ['metric']
        ]);
    }

    #[Route('/{gameSlug}/create', name: 'create', methods: 'POST')]
    #[IsGranted(GameVoter::VIEW, subject: 'game', message: ObreatlasExceptions::CANT_VIEW_GAME)]
    #[IsGranted(GameVoter::GAME_MASTER, subject: 'game', message: ObreatlasExceptions::NOT_GAME_MASTER)]
    public function create(
        #[MapEntity(mapping: ['gameSlug' => 'slug'])]
        Game                   $game,
        #[MapRequestPayload(
            serializationContext: [
                'groups' => ['metric.create']
            ]
        )]
        Metric                 $metric,
        EntityManagerInterface $em
    ): JsonResponse
    {
        $metric->setGame($game);

        $em->persist($metric);
        $em->flush();

        return self::response($metric, Response::HTTP_CREATED, [], [
            'groups' => ['metric']
        ]);
    }

    /** @throws Exception */
    #[Route('/{metricId}/edit', name: 'edit', methods: 'POST')]
    public function edit(
        #[MapEntity(mapping: ['metricId' => 'id'])]
        Metric                 $metric,
        #[MapRequestPayload]
        EditMetricDTO          $metricDTO,
        Security               $security,
        EntityManagerInterface $em
    ): JsonResponse
    {
        $isGameMaster = $security->isGranted(GameVoter::GAME_MASTER, $metric->getGame());
        if (!$isGameMaster) {
            throw new Exception(ObreatlasExceptions::NOT_GAME_MASTER);
        }

        $metric
            ->setName($metricDTO->name)
            ->setEmoji($metricDTO->emoji);

        $em->flush();

        return self::response($metric, Response::HTTP_OK, [], [
            'groups' => ['metric']
        ]);
    }

    /** @throws Exception */
    #[Route('/{protagonistId}/assign', name: 'assign', methods: 'POST')]
    #[IsGranted(ProtagonistVoter::VIEW, subject: 'protagonist', message: ObreatlasExceptions::CANT_VIEW_PROTAGONIST)]
    public function assign(
        #[MapEntity(mapping: ['protagonistId' => 'id'])]
        Protagonist            $protagonist,
        #[MapRequestPayload]
        AssignMetricsDTO       $assignMetricsDTO,
        MetricRepository $metricRepository,
        ProtagonistMetricRepository $protagonistMetricRepository,
        Security               $security,
        EntityManagerInterface $em,
    ): JsonResponse
    {
        $isGameMaster = $security->isGranted(GameVoter::GAME_MASTER, $protagonist->getGame());
        if (!$isGameMaster) {
            throw new Exception(ObreatlasExceptions::NOT_GAME_MASTER);
        }

        $protagonist->removeAllMetrics();

        foreach ($assignMetricsDTO->metrics as $metricDTO) {
            $metric = $metricRepository->find($metricDTO->metricDetails->id);

            if (!$metric) {
                throw new Exception(ObreatlasExceptions::METRIC_NOT_FOUND);
            }

            if ($metricDTO->id) {
                $protagonistMetric = $protagonistMetricRepository->find($metricDTO->id);

                if (!$protagonistMetric) {
                    throw new Exception(ObreatlasExceptions::PROTAGONIST_METRIC_NOT_FOUND);
                }
            } else {
                $protagonistMetric = new ProtagonistMetric();
            }

            $protagonistMetric
                ->setMetricDetails($metric)
                ->setValue($metricDTO->value)
                ->setMax($metricDTO->max);

            $em->persist($protagonistMetric);

            $protagonist->addMetric($protagonistMetric);
        }

        $em->persist($protagonist);

        $em->flush();

        return self::response($protagonist, Response::HTTP_OK, [], [
            'groups' => ['protagonist', 'user', 'metric']
        ]);
    }
}
