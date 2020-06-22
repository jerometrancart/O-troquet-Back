<?php
namespace App\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

class MailerService extends AbstractController
{
    /**
     *
     */
    private $mailer ;
    public function __construct(MailerInterface $mailer)
    {
      
        $this->mailer = $mailer;
    }

    /**
     * Undocumented function
     *
     * @param [type] $token
     * @param [type] $to
     * @param [type] $username
     * @param [type] $tokenLifeTime
     * @param [type] $subject
     * @param [type] $template
     * @return void
     */
    public function sendToken($token, $to, $username,$tokenLifeTime,$subject,$template)
    {
        //dd($token, $to, $username,$tokenLifeTime,$subject,$template);
        $email = (new TemplatedEmail())
        ->from(new Address('essaiphpmailer@gmail.com', 'Ne pas répondre'))
        ->to($to)
        ->subject($subject)
        ->htmlTemplate($template)
        ->context([
            'Token' => $token,
            'username' => $username,
            'tokenLifetime' => $tokenLifeTime,
        ]);

        $this->mailer->send($email);
    }
}