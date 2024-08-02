<?php

namespace App\Service;

use App\Entity\Game;
use App\Exceptions\ObreatlasExceptions;
use App\Security\Voter\GameVoter;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

readonly class VoterService
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * Check if the current user is the game master for the given game
     *
     * @param Game $game
     * @return bool
     * @throws AccessDeniedException if the user is not the game master
     */
    public function isGameMaster(Game $game): bool
    {
        $isGameMaster = $this->security->isGranted(GameVoter::GAME_MASTER, $game);

        if (!$isGameMaster) {
            throw new AccessDeniedException(ObreatlasExceptions::NOT_GAME_MASTER);
        }

        return true;
    }
}