<?php

namespace App\Controller;

use App\DTO\Game\EditGameDTO;
use App\Entity\Game;
use App\Exceptions\ObreatlasExceptions;
use App\Repository\GameRepository;
use App\Security\Voter\GameVoter;
use App\Service\SluggerService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/games', name: 'games')]
class GameController extends BaseController
{
    #[Route(name: 'getAll', methods: 'GET')]
    public function getAll(GameRepository $gameRepository): JsonResponse
    {
        $user = $this->getUser();

        $games = $gameRepository->getGamesAvailableByUser($user->getUserIdentifier());

        return self::response($games, Response::HTTP_OK, [], [
            'groups' => ['game', 'user']
        ]);
    }

    /** @throws Exception */
    #[Route('/create', name: 'create', methods: 'POST')]
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
        SluggerService::validateSlug($game->getTitle(), $game->getSlug());

        $user = $this->getUser();

        $game
            ->setGameMaster($user)
            ->setCreator($user)
            ->setClosed(false);

        $em->persist($game);
        $em->flush();

        return self::response($game, Response::HTTP_CREATED, [], [
            'groups' => ['game', 'user']
        ]);
    }

    #[Route('/{gameSlug}', name: 'get', methods: 'GET')]
    #[IsGranted(GameVoter::VIEW, subject: 'game', message: ObreatlasExceptions::CANT_VIEW_GAME)]
    public function get(
        #[MapEntity(mapping: ['gameSlug' => 'slug'])]
        Game $game,
    ): JsonResponse
    {
        $user = $this->getUser();

        $game->filterProtagonistsAvailableByUser($user);

        return self::response($game, Response::HTTP_OK, [], [
            'groups' => ['game', 'game.lobby', 'protagonist', 'user']
        ]);
    }

    /** @throws Exception */
    #[Route('/{gameId}/edit', name: 'edit', methods: 'POST')]
    #[IsGranted(GameVoter::VIEW, subject: 'game', message: ObreatlasExceptions::CANT_VIEW_GAME)]
    #[IsGranted(GameVoter::GAME_MASTER, subject: 'game', message: ObreatlasExceptions::NOT_GAME_MASTER)]
    public function edit(
        #[MapEntity(mapping: ['gameId' => 'id'])]
        Game $game,
        #[MapRequestPayload]
        EditGameDTO $gameDTO,
        GameRepository $gameRepository,
        EntityManagerInterface $em,
    ): JsonResponse
    {
        SluggerService::validateSlug($gameDTO->title, $gameDTO->slug);

        // Game title update
        if ($gameDTO->slug !== $game->getSlug()) {
            $matchedGame = $gameRepository->findOneBy(['slug' => $gameDTO->slug]);
            if ($matchedGame) {
                throw new Exception(ObreatlasExceptions::GAME_EXIST);
            }
        }

        $game
            ->setTitle($gameDTO->title)
            ->setSlug($gameDTO->slug)
            ->setStartedAt($gameDTO->startedAt)
            ->setClosed($gameDTO->closed);

        $em->flush();

        return self::response($game, Response::HTTP_OK, [], [
            'groups' => ['game', 'user']
        ]);
    }
}
