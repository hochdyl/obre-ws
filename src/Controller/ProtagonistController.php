<?php

namespace App\Controller;

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
    #[Route('/{gameSlug}', name: 'getAll', methods: 'GET')]
    #[IsGranted(GameVoter::VIEW, subject: 'game', message: "You can't view this game")]
    public function getAll(
        #[MapEntity(mapping: ['gameSlug' => 'slug'])]
        Game $game,
        Security $security
    ): JsonResponse
    {
        $user = $this->getUser();

        $isOwner = $security->isGranted(GameVoter::EDIT, $game);

        $protagonists = $isOwner ?
            $game->getProtagonists() :
            $game->getProtagonistsAvailableByUser($user);

        return self::response($protagonists, Response::HTTP_OK, [], [
            'groups' => ['protagonist']
        ]);
    }

    /** @throws Exception */
    #[Route('/{gameSlug}', name: 'create', methods: 'POST')]
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
            'groups' => ['protagonist']
        ]);
    }

    #[Route('/choose/{protagonistId}', name: 'choose', methods: 'GET')]
    #[IsGranted(ProtagonistVoter::CHOOSE, subject: 'protagonist', message: "You can't choose this protagonist")]
    public function choose(
        #[MapEntity(mapping: ['protagonistId' => 'id'])]
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