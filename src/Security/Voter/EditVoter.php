<?php

namespace App\Security\Voter;

use App\Entity\Question;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class EditVoter extends Voter
{
    protected function supports($attribute, $subject)
    {
        return in_array($attribute, ['edit','onlyuser'])
            && $subject instanceof User;
    }

    protected function voteOnAttribute($attribute, $u, TokenInterface $token)
    {
    
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }
        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case 'edit':
                // Si l'utilisateur connecté est l'auteur de la question, on autorise sa modification
                if ($user->getId() == $u->getId()) {
                    return true;
                }
                // Si l'utilisateur a un ROLE_ADMIN ou un ROLE_MODERATOR, on retourne true
                if (in_array($user->getRoles()[0], ['ROLE_ADMIN', 'ROLE_SUPER_ADMIN'])) {
                    return true;
                }
            case 'onlyuser':
                     // Si l'utilisateur connecté est l'auteur de la question, on autorise sa modification
                if ($user->getId() == $u->getId()) {
                    return true;
                }
                break;
        }
        
        return false;
    }
}