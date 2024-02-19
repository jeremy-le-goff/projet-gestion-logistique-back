<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;


class UserVoter extends Voter
{
    public const ROLE_SUPERADMIN = 'ROLE_SUPERADMIN';
    public const ROLE_ADMIN = 'ROLE_ADMIN';
    public const ROLE_MANAGER = 'ROLE_MANAGER';
    public const ROLE_LOGISTICIAN = 'ROLE_LOGISTICIAN';

    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, [self::ROLE_SUPERADMIN, self::ROLE_ADMIN, self::ROLE_MANAGER, self::ROLE_LOGISTICIAN]);
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser() instanceof UserInterface && $token->getUser()->getRoles();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }
        switch ($attribute) {

            case self::ROLE_SUPERADMIN:
                if ($user instanceof UserInterface && in_array(self::ROLE_SUPERADMIN, $user->getRoles())) {
                    return VoterInterface::ACCESS_GRANTED;
                }
                break;
            case self::ROLE_ADMIN:
                if ($user instanceof UserInterface && in_array(self::ROLE_ADMIN, $user->getRoles())) {
                    return VoterInterface::ACCESS_GRANTED;
                }
                break;
            case self::ROLE_MANAGER:
                if ($user instanceof UserInterface && in_array(self::ROLE_MANAGER, $user->getRoles())) {
                    return VoterInterface::ACCESS_GRANTED;
                }
                break;
            case self::ROLE_LOGISTICIAN:
                if ($user instanceof UserInterface && in_array(self::ROLE_LOGISTICIAN, $user->getRoles())) {
                    return VoterInterface::ACCESS_GRANTED;
                }
                break;
            default:
                return VoterInterface::ACCESS_DENIED;
        }
    }
}
