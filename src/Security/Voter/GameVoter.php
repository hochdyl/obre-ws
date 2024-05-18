<?php

namespace App\Security\Voter;

use App\Entity\Game;
use App\Entity\Protagonist;
use App\Entity\User;
use App\Service\UserService;
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
                $protagonistsOwned = $subject->getProtagonistsAvailableByUser($user);

                if(count($protagonistsOwned) > 0) return true;
                if ($subject->isClosed()) return false;
                return true;

            case self::EDIT:
                return $subject->getOwner()->getUserIdentifier() === $user->getUserIdentifier();
        }

        return false;
    }
}
