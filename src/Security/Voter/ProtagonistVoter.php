<?php

namespace App\Security\Voter;

use App\Entity\Protagonist;
use App\Entity\User;
use Exception;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ProtagonistVoter extends Voter
{
    public const CHOOSE = 'PROTAGONIST_CHOOSE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::CHOOSE])
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
            case self::CHOOSE:
                return !$subject->getOwner();
        }

        return false;
    }
}
