<?php

namespace App\Controller;

use App\DTO\Metric\EditMetricsDTO;
use App\Entity\Game;
use App\Entity\Protagonist;
use App\Exceptions\ObreatlasExceptions;
use App\Repository\MetricRepository;
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
    /** @throws Exception */
    #[Route('/{gameId}', name: 'getAll', methods: 'GET')]
    #[IsGranted(GameVoter::VIEW, subject: 'game', message: ObreatlasExceptions::CANT_VIEW_GAME)]
    public function getAll(
        #[MapEntity(mapping: ['gameId' => 'id'])]
        Game $game,
        MetricRepository $metricRepository,
        Security $security,
    ): JsonResponse
    {
        $isGameMaster = $security->isGranted(GameVoter::GAME_MASTER, $game);
        if (!$isGameMaster) {
            throw new Exception(ObreatlasExceptions::NOT_GAME_MASTER);
        }

        $metrics = $metricRepository->findAllByGame($game->getId());

        return self::response($metrics, Response::HTTP_OK, [], [
            'groups' => ['metric']
        ]);
    }

    /** @throws Exception */
    #[Route('/{protagonistId}/edit', name: 'edit', methods: 'POST')]
    #[IsGranted(ProtagonistVoter::VIEW, subject: 'protagonist', message: ObreatlasExceptions::CANT_VIEW_PROTAGONIST)]
    public function edit(
        #[MapEntity(mapping: ['protagonistId' => 'id'])]
        Protagonist $protagonist,
        #[MapRequestPayload]
        EditMetricsDTO $metricsDTO,
        Security $security,
        MetricService $metricService,
        EntityManagerInterface $em,
    ): JsonResponse
    {
        $isGameMaster = $security->isGranted(GameVoter::GAME_MASTER, $protagonist->getGame());
        if (!$isGameMaster) {
            throw new Exception(ObreatlasExceptions::NOT_GAME_MASTER);
        }

        // List of protagonist metrics id to save
        $savedProtagonistMetrics = [];

        $metricsDTO = $metricsDTO->metrics;
        foreach ($metricsDTO as $metricDTO) {
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
