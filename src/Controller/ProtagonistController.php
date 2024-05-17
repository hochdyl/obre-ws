<?php

namespace App\Controller;

use App\Entity\Protagonist;
use App\Repository\GameRepository;
use App\Repository\ProtagonistRepository;
use App\Service\SluggerService;
use App\Service\UploaderService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use App\Exceptions\ObreatlasExceptions;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/protagonists', name: 'protagonists')]
class ProtagonistController extends BaseController
{
    /**
     * Return game's protagonists
     *
     * @param string $gameSlug
     * @param GameRepository $gameRepository
     * @return JsonResponse
     */
    #[Route('/{gameSlug}', name: 'getAll', methods: 'GET')]
    public function getAll(string $gameSlug, GameRepository $gameRepository): JsonResponse
    {
        $game = $gameRepository->findOneBy(['slug' => $gameSlug]);
        $protagonists = $game->getProtagonists();

        return self::response($protagonists, Response::HTTP_OK, [], [
            'groups' => ['protagonist']
        ]);
    }

    /**
     * Create a game protagonist
     *
     * @param string $gameSlug
     * @param Protagonist $protagonist
     * @param ProtagonistRepository $protagonistRepository
     * @param GameRepository $gameRepository
     * @param EntityManagerInterface $em
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    #[Route('/{gameSlug}', name: 'create', methods: 'POST')]
    public function create(
        string $gameSlug,
        #[MapRequestPayload(
            serializationContext: [
                'groups' => ['protagonist.create']
            ]
        )] Protagonist $protagonist,
        ProtagonistRepository $protagonistRepository,
        GameRepository $gameRepository,
        EntityManagerInterface $em,
        Request $request,
    ): JsonResponse
    {
        $game = $gameRepository->findOneBy(['slug' => $gameSlug]);
        if (!$game) {
            throw new Exception(ObreatlasExceptions::GAME_NOT_FOUND);
        }

        $slug = SluggerService::getSlug($protagonist->getName());
        if ($slug !== $protagonist->getSlug()) {
            throw new Exception(ObreatlasExceptions::SLUG_NOT_MATCH_NAME);
        }

        $matchedProtagonist = $protagonistRepository->findByGameAndSlug($gameSlug, $protagonist->getSlug());
        if ($matchedProtagonist) {
            throw new Exception(ObreatlasExceptions::PROTAGONIST_EXIST);
        }

        $user = $this->getUser();

        $protagonist
            ->setGame($game)
            ->setCreator($user);

        $portrait = $request->files->get('portrait');

        if ($portrait) {
            $upload = UploaderService::upload($portrait, $user);
            $protagonist->setPortrait($upload);
        }

        $em->persist($protagonist);
        $em->flush();

        return self::response($protagonist, Response::HTTP_CREATED, [], [
            'groups' => ['protagonist']
        ]);
    }

    #[Route('/choose/{protagonist}', name: 'choose', methods: 'GET')]
    public function choose(
        Protagonist $protagonist,
        EntityManagerInterface $em
    ): JsonResponse
    {
        $user = $this->getUser();

        $protagonist->setOwner($user);

        $em->flush();

        return self::response($protagonist, Response::HTTP_OK, [], [
            'groups' => ['protagonist']
        ]);
    }
}