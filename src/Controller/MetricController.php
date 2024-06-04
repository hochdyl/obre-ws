<?php

namespace App\Controller;

use App\DTO\Metric\EditMetricsValuesDTO;
use App\Entity\Game;
use App\Entity\Metric;
use App\Entity\Protagonist;
use App\Exceptions\ObreatlasExceptions;
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
        Game $game,
        #[MapRequestPayload(
            serializationContext: [
                'groups' => ['metric.create']
            ]
        )]
        Metric $metric,
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
    #[Route('/{protagonistId}/editAll', name: 'editAll', methods: 'POST')]
    #[IsGranted(ProtagonistVoter::VIEW, subject: 'protagonist', message: ObreatlasExceptions::CANT_VIEW_PROTAGONIST)]
    public function edit(
        #[MapEntity(mapping: ['protagonistId' => 'id'])]
        Protagonist            $protagonist,
        #[MapRequestPayload]
        EditMetricsValuesDTO   $metricsValuesDTO,
        Security               $security,
        MetricService          $metricService,
        EntityManagerInterface $em,
    ): JsonResponse
    {
        $isGameMaster = $security->isGranted(GameVoter::GAME_MASTER, $protagonist->getGame());
        if (!$isGameMaster) {
            throw new Exception(ObreatlasExceptions::NOT_GAME_MASTER);
        }

        // List of protagonist metrics id to save
        $savedProtagonistMetrics = [];

        foreach ($metricsValuesDTO->metricsValues as $metricDTO) {
            $data = $metricService->getData($metricDTO, $protagonist);

            $metric = $data['metric'];
            $protagonistMetric = $data['protagonistMetric'];

            $savedProtagonistMetrics[] = $protagonistMetric->getId();

            $em->persist($metric);
            $em->persist($protagonistMetric);
        }

        // Remove protagonist metrics that are not in list
        $protagonistMetrics = $protagonist->getMetricsValues();
        foreach ($protagonistMetrics as $protagonistMetric) {
            if (!in_array($protagonistMetric->getId(), $savedProtagonistMetrics)) {
                $em->remove($protagonistMetric);
            }
        }

        $em->flush();

        return self::response($protagonist, Response::HTTP_OK, [], [
            'groups' => ['protagonist', 'user']
        ]);
    }
}
