<?php

namespace App\Controller;

use App\DTO\Game\EditGameDTO;
use App\Entity\Game;
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
            'groups' => ['game']
        ]);
    }

    #[Route('/{gameSlug}', name: 'get', methods: 'GET')]
    #[IsGranted(GameVoter::VIEW, subject: 'game', message: "You can't view this game")]
    public function get(
        #[MapEntity(mapping: ['gameSlug' => 'slug'])]
        Game $game,
    ): JsonResponse
    {
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
        SluggerService::validateSlug($game->getTitle(), $game->getSlug());

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

    /**
     * @throws Exception
     */
    #[Route('/{gameSlug}', name: 'edit', methods: 'PUT')]
    #[IsGranted(GameVoter::EDIT, subject: 'game', message: "You can't edit this game")]
    public function edit(
        #[MapEntity(mapping: ['gameSlug' => 'slug'])]
        Game $game,
        #[MapRequestPayload]
        EditGameDTO $gameDTO,
        EntityManagerInterface $em,
    ): JsonResponse
    {
        SluggerService::validateSlug($game->getTitle(), $game->getSlug());

        $game->setTitle($gameDTO->title)
            ->setSlug($gameDTO->slug)
            ->setStartedAt($gameDTO->startedAt)
            ->setClosed($gameDTO->closed);

        $em->flush();

        return self::response($game, Response::HTTP_OK, [], [
            'groups' => ['game']
        ]);
    }
}
