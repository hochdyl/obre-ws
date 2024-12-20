<?php

namespace App\Controller;

use App\DTO\Protagonist\EditProtagonistDTO;
use App\Entity\Game;
use App\Entity\Protagonist;
use App\Exceptions\ObreatlasExceptions;
use App\Repository\ProtagonistRepository;
use App\Repository\UploadRepository;
use App\Security\Voter\GameVoter;
use App\Security\Voter\ProtagonistVoter;
use App\Service\SluggerService;
use App\Service\UploaderService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
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
        Game                   $game,
        #[MapRequestPayload(
            serializationContext: [
                'groups' => ['protagonist.create', 'upload']
            ]
        )]
        Protagonist            $protagonist,
        ProtagonistRepository  $protagonistRepository,
        UploadRepository       $uploadRepository,
        EntityManagerInterface $em,
    ): JsonResponse
    {
        SluggerService::validateSlug($protagonist->getName(), $protagonist->getSlug());

        $matchedProtagonist = $protagonistRepository->findByGameAndSlug($game->getSlug(), $protagonist->getSlug());
        if ($matchedProtagonist) {
            throw new Exception(ObreatlasExceptions::PROTAGONIST_EXIST);
        }

        $portrait = null;
        if ($protagonist->getPortrait()) {
            $portrait = $uploadRepository->findOneBy(['fileName' => $protagonist->getPortrait()->getFileName()]);
            if (!$portrait) {
                throw new Exception(ObreatlasExceptions::FILE_NOT_FOUND);
            }
        }

        $user = $this->getUser();

        $protagonist
            ->setGame($game)
            ->setCreator($user)
            ->setLevel(1)
            ->setPortrait($portrait);

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
        Protagonist            $protagonist,
        Security               $security,
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
    public function edit(
        #[MapEntity(mapping: ['protagonistId' => 'id'])]
        Protagonist            $protagonist,
        #[MapRequestPayload]
        EditProtagonistDTO     $protagonistDTO,
        ProtagonistRepository  $protagonistRepository,
        UploadRepository       $uploadRepository,
        Security               $security,
        EntityManagerInterface $em
    ): JsonResponse
    {
        SluggerService::validateSlug($protagonistDTO->name, $protagonistDTO->slug);

        $isGameMaster = $security->isGranted(GameVoter::GAME_MASTER, $protagonist->getGame());
        if (!$isGameMaster) {
            throw new Exception(ObreatlasExceptions::NOT_GAME_MASTER);
        }

        // Protagonist name update
        if ($protagonistDTO->slug !== $protagonist->getSlug()) {
            $matchedProtagonist = $protagonistRepository->findByGameAndSlug($protagonist->getGame()->getSlug(), $protagonistDTO->slug);
            if ($matchedProtagonist) {
                throw new Exception(ObreatlasExceptions::PROTAGONIST_EXIST);
            }
        }

        $portrait = null;
        if ($protagonistDTO->portrait->getFileName()) {
            $portrait = $uploadRepository->findOneBy(['fileName' => $protagonistDTO->portrait->getFileName()]);
            if (!$portrait) {
                throw new Exception(ObreatlasExceptions::FILE_NOT_FOUND);
            }
        }

        $protagonist
            ->setName($protagonistDTO->name)
            ->setSlug($protagonistDTO->slug)
            ->setStory($protagonistDTO->story)
            ->setLevel($protagonistDTO->level)
            ->setPortrait($portrait);

        $em->flush();

        return self::response($protagonist, Response::HTTP_OK, [], [
            'groups' => ['protagonist', 'user']
        ]);
    }
}