<?php

namespace App\Service;

use App\Entity\Game;
use App\Exceptions\ObreatlasExceptions;
use App\Security\Voter\GameVoter;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

readonly class VoterService
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * Check if the current user is the game master
     *
     * @param Game $game
     * @return bool
     * @throws AccessDeniedHttpException
     */
    public function isGameMaster(Game $game): bool
    {
        $isGameMaster = $this->security->isGranted(GameVoter::GAME_MASTER, $game);

        if (!$isGameMaster) {
            throw new AccessDeniedHttpException(ObreatlasExceptions::NOT_GAME_MASTER);
        }

        return true;
    }

    /**
     * Check if the current user can view the game
     *
     * @param Game $game
     * @return bool
     * @throws AccessDeniedHttpException
     */
    public function canViewGame(Game $game): bool
    {
        $canViewGame = $this->security->isGranted(GameVoter::GAME_MASTER, $game);

        if (!$canViewGame) {
            throw new AccessDeniedHttpException(ObreatlasExceptions::CANT_VIEW_GAME);
        }

        return true;
    }
}