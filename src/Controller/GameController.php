<?php

namespace App\Controller;

use App\Entity\Game;
use App\Repository\GameRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/games', name: 'games')]
class GameController extends BaseController
{
    /**
     * Return all games with the newest first
     *
     * @param GameRepository $gameRepository
     * @return JsonResponse
     */
    #[Route(name: 'getAll', methods: 'GET')]
    public function getAll(GameRepository $gameRepository): JsonResponse
    {
        $games = $gameRepository->findBy([], ['id' => 'DESC']);

        return self::response($games, Response::HTTP_OK, [], [
            'groups' => ['game']
        ]);
    }

    /**
     * Return a game by id
     *
     * @param int $id
     * @param GameRepository $gameRepository
     * @return JsonResponse
     */
    #[Route('/{id}', name: 'get', methods: 'GET')]
    public function get(int $id, GameRepository $gameRepository): JsonResponse
    {
        $game = $gameRepository->find($id);

        return self::response($game, Response::HTTP_OK, [], [
            'groups' => ['game']
        ]);
    }

    /**
     * Create a new game
     *
     * @param Game $game
     * @param EntityManagerInterface $em
     * @return JsonResponse
     */
    #[Route(name: 'create', methods: 'POST')]
    public function create(
        #[MapRequestPayload(
            serializationContext: [
                'groups' => ['game.create']
            ]
        )] Game                $game,
        EntityManagerInterface $em,
    ): JsonResponse
    {
        $user = $this->getUser();
        $game->setOwner($user);

        $em->persist($game);
        $em->flush();

        return self::response($game, Response::HTTP_CREATED, [], [
            'groups' => ['game']
        ]);
    }
}
