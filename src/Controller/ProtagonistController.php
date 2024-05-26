<?php

namespace App\Controller;

use App\DTO\Protagonist\EditProtagonistDTO;
use App\Entity\Game;
use App\Entity\Protagonist;
use App\Repository\ProtagonistRepository;
use App\Security\Voter\GameVoter;
use App\Security\Voter\ProtagonistVoter;
use App\Service\SluggerService;
use App\Service\UploaderService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use App\Exceptions\ObreatlasExceptions;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/protagonists', name: 'protagonists')]
class ProtagonistController extends BaseController
{
    /** @throws Exception */
    #[Route('/{gameSlug}/create', name: 'create', methods: 'POST')]
    #[IsGranted(GameVoter::VIEW, subject: 'game', message: ObreatlasExceptions::CANT_VIEW_GAME)]
    public function create(
        #[MapEntity(mapping: ['gameSlug' => 'slug'])]
        Game $game,
        #[MapRequestPayload(
            serializationContext: [
                'groups' => ['protagonist.create']
            ]
        )]
        Protagonist $protagonist,
        ProtagonistRepository $protagonistRepository,
        EntityManagerInterface $em,
        Request $request,
    ): JsonResponse
    {
        SluggerService::validateSlug($protagonist->getName(), $protagonist->getSlug());

        $matchedProtagonist = $protagonistRepository->findByGameAndSlug($game->getSlug(), $protagonist->getSlug());
        if ($matchedProtagonist) {
            throw new Exception(ObreatlasExceptions::PROTAGONIST_EXIST);
        }

        $user = $this->getUser();

        $protagonist->setGame($game)
            ->setCreator($user);

        $portrait = $request->files->get('portrait');

        if ($portrait) {
            $upload = UploaderService::upload($portrait, $user);
            $protagonist->setPortrait($upload);
        }

        $em->persist($protagonist);
        $em->flush();

        return self::response($protagonist, Response::HTTP_CREATED, [], [
            'groups' => ['protagonist', 'user']
        ]);
    }

    /** @throws Exception */
    #[Route('/{protagonistId}/choose', name: 'choose', methods: 'GET')]
    #[IsGranted(ProtagonistVoter::VIEW, subject: 'protagonist', message: ObreatlasExceptions::CANT_VIEW_PROTAGONIST)]
    #[IsGranted(ProtagonistVoter::CHOOSE, subject: 'protagonist', message: ObreatlasExceptions::CANT_CHOOSE_PROTAGONIST)]
    public function choose(
        #[MapEntity(mapping: ['protagonistId' => 'id'])]
        Protagonist $protagonist,
        Security $security,
        EntityManagerInterface $em
    ): JsonResponse
    {
        $canViewGame = $security->isGranted(GameVoter::VIEW, $protagonist->getGame());
        if (!$canViewGame) {
            throw new Exception(ObreatlasExceptions::CANT_VIEW_GAME);
        }

        $user = $this->getUser();

        $protagonist->setOwner($user);

        $em->flush();

        return self::response($protagonist, Response::HTTP_OK, [], [
            'groups' => ['protagonist', 'user']
        ]);
    }

    /** @throws Exception */
    #[Route('/{protagonistId}/edit', name: 'edit', methods: 'POST')]
    #[IsGranted(ProtagonistVoter::VIEW, subject: 'protagonist', message: ObreatlasExceptions::CANT_VIEW_PROTAGONIST)]
    #[IsGranted(ProtagonistVoter::EDIT, subject: 'protagonist', message: ObreatlasExceptions::CANT_EDIT_PROTAGONIST)]
    public function edit(
        #[MapEntity(mapping: ['protagonistId' => 'id'])]
        Protagonist $protagonist,
        #[MapRequestPayload]
        EditProtagonistDTO $protagonistDTO,
        Security $security,
        EntityManagerInterface $em,
        Request $request,
    ): JsonResponse
    {
        SluggerService::validateSlug($protagonistDTO->name, $protagonistDTO->slug);

        $canViewGame = $security->isGranted(GameVoter::VIEW, $protagonist->getGame());
        if (!$canViewGame) {
            throw new Exception(ObreatlasExceptions::CANT_VIEW_GAME);
        }

        $user = $this->getUser();

        $protagonist->setName($protagonistDTO->name)
            ->setSlug($protagonistDTO->slug)
            ->setStory($protagonistDTO->story);

        $portrait = $request->files->get('portrait');

        if ($portrait) {
            $upload = UploaderService::upload($portrait, $user);
            $protagonist->setPortrait($upload);
        }

        $em->flush();

        return self::response($protagonist, Response::HTTP_OK, [], [
            'groups' => ['protagonist', 'user']
        ]);
    }

    /** @throws Exception */
    #[Route('/{gameSlug}/{protagonistSlug}', name: 'get', methods: 'GET')]
    #[IsGranted(GameVoter::VIEW, subject: 'game', message: ObreatlasExceptions::CANT_VIEW_GAME)]
    public function get(
        #[MapEntity(mapping: ['gameSlug' => 'slug'])]
        Game $game,
        string $protagonistSlug,
        ProtagonistRepository $protagonistRepository,
        Security $security
    ): JsonResponse
    {
        $matchedProtagonist = $protagonistRepository->findByGameAndSlug($game->getSlug(), $protagonistSlug);
        if (!$matchedProtagonist) {
            throw new Exception(ObreatlasExceptions::PROTAGONIST_NOT_EXIST);
        }

        $canViewProtagonist = $security->isGranted(ProtagonistVoter::VIEW, $matchedProtagonist);
        if (!$canViewProtagonist) {
            throw new Exception(ObreatlasExceptions::CANT_VIEW_PROTAGONIST);
        }

        return self::response($matchedProtagonist, Response::HTTP_OK, [], [
            'groups' => ['protagonist', 'protagonist.dashboard', 'user', 'game']
        ]);
    }
}