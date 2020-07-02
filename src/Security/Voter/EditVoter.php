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
        // https://symfony.com/doc/current/security/voters.html
        // Lorsqu'on exécute denyAccessUnlessGranted() dans une contrôleur
        // Tous les voters sont instanciés, et leurs méthodes supports() sont toutes exécutées
        // Si supports() retourne TRUE, la méthode voteOnAttribute() est exécutée

        // $attribute est une chaine de caractères,
        // on teste si elle est présente dans une liste de droits gérés par ce Voter
        // $subject est l'objet concerné, on attend un objet de la classe Question
        return in_array($attribute, ['edit','onlyuser'])
            && $subject instanceof User;
    }

    protected function voteOnAttribute($attribute, $u, TokenInterface $token)
    {
        // voteOnAttribute() doit retourner true si l'utilisateur a le droit $attribute
        // et false sinon
        
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