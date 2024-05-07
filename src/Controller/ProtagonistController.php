<?php

namespace App\Controller;

use App\Entity\Protagonist;
use App\Repository\GameRepository;
use App\Service\SluggerService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/{gameSlug}/protagonists', name: 'protagonists')]
class ProtagonistController extends BaseController
{
    /**
     * Return game's protagonists by slug
     *
     * @param string $gameSlug
     * @param GameRepository $gameRepository
     * @return JsonResponse
     */
    #[Route(name: 'getProtagonists', methods: 'GET')]
    public function getProtagonists(string $gameSlug, GameRepository $gameRepository): JsonResponse
    {
        $game = $gameRepository->findOneBy(['slug' => $gameSlug]);
        $protagonists = $game->getProtagonists();

        return self::response($protagonists, Response::HTTP_OK, [], [
            'groups' => ['protagonist']
        ]);
    }

    /**
     * Create a new game protagonist
     *
     * @param string $gameSlug
     * @param Protagonist $protagonist
     * @param GameRepository $gameRepository
     * @param EntityManagerInterface $em
     * @return JsonResponse
     * @throws Exception
     */
    #[Route(name: 'create', methods: 'POST')]
    public function create(
        string $gameSlug,
        #[MapRequestPayload(
            serializationContext: [
                'groups' => ['protagonist.create']
            ]
        )] Protagonist $protagonist,
        GameRepository $gameRepository,
        EntityManagerInterface $em,
    ): JsonResponse
    {
        $slug = SluggerService::getSlug($protagonist->getSlug());

        if ($slug !== $protagonist->getSlug()) {
            throw new Exception('Slug does not match with name');
        }

        $game = $gameRepository->findOneBy(['slug' => $gameSlug]);
        $protagonist->setGame($game);

        $em->persist($protagonist);
        $em->flush();

        return self::response($protagonist, Response::HTTP_CREATED, [], [
            'groups' => ['protagonist']
        ]);
    }
}