<?php

namespace App\Security\Voter;

use App\Entity\Protagonist;
use App\Entity\User;
use Exception;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ProtagonistVoter extends Voter
{
    public const VIEW = 'PROTAGONIST_VIEW';
    public const CHOOSE = 'PROTAGONIST_CHOOSE';
    public const EDIT = 'PROTAGONIST_EDIT';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::VIEW, self::CHOOSE, self::EDIT])
            && $subject instanceof Protagonist;
    }

    /** @throws Exception */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User || !$subject instanceof Protagonist) {
            return false;
        }

        switch ($attribute) {
            case self::VIEW:
                if (
                    $subject->getGame()->getOwner()->getUserIdentifier() === $user->getUserIdentifier() ||
                    !$subject->getOwner()
                ) return true;

                return $subject->getOwner()->getUserIdentifier() === $user->getUserIdentifier();

            case self::CHOOSE:
                return !$subject->getOwner();

            case self::EDIT:
                return (
                    $subject->getGame()->getOwner()->getUserIdentifier() === $user->getUserIdentifier() ||
                    ($subject->getOwner() && $subject->getOwner()->getUserIdentifier() === $user->getUserIdentifier()) ||
                    (!$subject->getOwner() && $subject->getCreator()->getUserIdentifier() === $user->getUserIdentifier())
                );
        }

        return false;
    }
}
