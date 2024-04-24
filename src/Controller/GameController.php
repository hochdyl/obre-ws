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
    #[Route(name: 'getAll', methods: 'GET')]
    public function getAll(GameRepository $gameRepository): JsonResponse
    {
        $games = $gameRepository->findAll();

        return self::response($games, Response::HTTP_OK, [], [
            'groups' => ['game']
        ]);
    }

    #[Route('/{id}', name: 'get', methods: 'GET')]
    public function get(int $id, GameRepository $gameRepository): JsonResponse
    {
        $game = $gameRepository->find($id);

        return self::response($game, Response::HTTP_OK, [], [
            'groups' => ['game']
        ]);
    }

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
