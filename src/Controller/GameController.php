<?php

namespace App\Controller;

use App\DTO\Game\EditGameDTO;
use App\Entity\Game;
use App\Repository\GameRepository;
use App\Service\SluggerService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use App\Exceptions\ObreatlasExceptions;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/games', name: 'games')]
class GameController extends BaseController
{
    #[Route(name: 'getAll', methods: 'GET')]
    public function getAll(GameRepository $gameRepository): JsonResponse
    {
        $user = $this->getUser();

        $games = $gameRepository->getGamesAvailableByUser($user->getUserIdentifier());

        return self::response($games, Response::HTTP_OK, [], [
            'groups' => ['game']
        ]);
    }

    #[Route('/{slug}', name: 'get', methods: 'GET')]
    public function get(string $slug, GameRepository $gameRepository): JsonResponse
    {
        $game = $gameRepository->findOneBy(['slug' => $slug]);

        return self::response($game, Response::HTTP_OK, [], [
            'groups' => ['game']
        ]);
    }

    /** @throws Exception */
    #[Route(name: 'create', methods: 'POST')]
    public function create(
        #[MapRequestPayload(
            serializationContext: [
                'groups' => ['game.create']
            ]
        )]
        Game $game,
        EntityManagerInterface $em,
    ): JsonResponse
    {
        $slug = SluggerService::getSlug($game->getTitle());
        if ($slug !== $game->getSlug()) {
            throw new Exception(ObreatlasExceptions::SLUG_NOT_MATCH_TITLE);
        }

        $user = $this->getUser();

        $game->setOwner($user)
            ->setCreator($user)
            ->setClosed(false);

        $em->persist($game);
        $em->flush();

        return self::response($game, Response::HTTP_CREATED, [], [
            'groups' => ['game']
        ]);
    }

    /** @throws Exception */
    #[Route('/{gameSlug}', name: 'edit', methods: 'PUT')]
    public function edit(
        #[MapEntity(mapping: ['gameSlug' => 'slug'])]
        Game $game,
        #[MapRequestPayload]
        EditGameDTO $gameDTO,
        EntityManagerInterface $em,
    ): JsonResponse
    {
        // TODO: bah finir ca en gros

        $slug = SluggerService::getSlug($game->getTitle());
        if ($slug !== $game->getSlug()) {
            throw new Exception(ObreatlasExceptions::SLUG_NOT_MATCH_TITLE);
        }

        $user = $this->getUser();

        $game
            ->setOwner($user)
            ->setCreator($user)
            ->setClosed(false);

        $em->persist($game);
        $em->flush();

        return self::response($game, Response::HTTP_CREATED, [], [
            'groups' => ['game']
        ]);
    }
}
