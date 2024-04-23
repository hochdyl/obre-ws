<?php

namespace App\Controller;

use App\Entity\Game;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/games', name: 'games')]
class GameController extends BaseController
{
    #[Route(name: 'get', methods: 'GET')]
    public function get(): JsonResponse
    {
        // TODO: get games or game with {id}
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/GameController.php',
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
