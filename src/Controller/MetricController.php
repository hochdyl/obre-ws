<?php

namespace App\Controller;

use App\DTO\Metric\EditMetricsDTO;
use App\Entity\Protagonist;
use App\Exceptions\ObreatlasExceptions;
use App\Security\Voter\GameVoter;
use App\Security\Voter\ProtagonistVoter;
use App\Service\MetricService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/metrics', name: 'metrics')]
class MetricController extends AbstractController
{
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

        foreach ($metricsDTO->metrics as $metricDTO) {
            $data = $metricService->getData($metricDTO, $protagonist);

            $em->persist($data['metric']);
            $em->persist($data['protagonistMetric']);
        }

        $em->flush();

        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/MetricController.php',
        ]);
    }
}
