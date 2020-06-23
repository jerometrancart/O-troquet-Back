<?php


namespace App\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Symfony\Component\Security\Core\User\UserInterface;

class AuthenticationSuccessListener
{

    /**
     * intercept /login_check
     * @param AuthenticationSuccessEvent $event
     */
    public function onAuthenticationSuccessResponse(AuthenticationSuccessEvent $event)
    {
        $data = $event->getData();
        $user = $event->getUser();

        if (!$user instanceof UserInterface) {
            return;
        }

        $data['metadata'] = array(
            'active' => $user->getIsActive(),
            'banned' => $user->getIsBanned(),
        );

        if ($user->getIsBanned() === true || $user->getIsActive() === false) {
            unset($data['token']);
        }
        $event->setData($data);
    }
}
