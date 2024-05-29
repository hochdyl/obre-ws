<?php

namespace App\Security\Voter;

use App\Entity\Game;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class GameVoter extends Voter
{
    public const VIEW = 'GAME_VIEW';
    public const EDIT = 'GAME_EDIT';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::VIEW, self::EDIT])
            && $subject instanceof Game;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User || !$subject instanceof Game) {
            return false;
        }

        switch ($attribute) {
            case self::VIEW:
                $protagonistsOwned = $subject->getProtagonistsOwnedByUser($user);
                if (
                    $subject->getGameMaster()->getUserIdentifier() === $user->getUserIdentifier() ||
                    count($protagonistsOwned) > 0
                ) return true;

                return !$subject->isClosed();

            case self::EDIT:
                return $subject->getGameMaster()->getUserIdentifier() === $user->getUserIdentifier();
        }

        return false;
    }
}
