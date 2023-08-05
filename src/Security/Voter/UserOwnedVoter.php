<?php

namespace App\Security\Voter;

use App\Entity\Post;
use App\Entity\User;
use App\Entity\UserOwnedInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class UserOwnedVoter extends Voter
{
    public const EDIT = 'CAN_EDIT';
    public const VIEW = 'CAN_VIEW';

    protected function supports(string $attribute, mixed $subject): bool
    {

        return in_array($attribute, [self::EDIT])
            && $subject instanceof UserOwnedInterface;
    }

    /**
     * @param string $attribute
     * @param UserOwnedInterface $subject
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof User) {
            return false;
        }

        $owner = $subject->getAuthor();

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::EDIT:
                return $owner && $owner->getUserIdentifier() === $user->getUserIdentifier();
            case self::VIEW:
                // logic to determine if the user can VIEW
                // return true or false
                break;
        }

        return false;
    }
}
