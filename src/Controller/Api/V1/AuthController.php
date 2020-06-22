<?php

namespace App\Controller\Api\V1;


use App\Entity\User;
use App\Controller\Api\V1\ApiController;
use App\Form\RegistrationFormType;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class AuthController extends ApiController
{




    /**
     * 
     * @Route("/api/register", name="api_register", methods={"POST"})
     * 
     */
    public function register(MailerInterface $mailer,Request $request, UserPasswordEncoderInterface $encoder)
    {




        $user = new User;
        $form = $this->createForm(RegistrationFormType::class, $user, ['csrf_protection' => false]);

        $json = json_decode($request->getContent(), true);

        $form->submit($json);

        if ($form->isValid()) {

            //dd($user->getEmail());

            $confirmationToken = $user->setConfirmationToken($this->generateToken());
            $user->setPassword($encoder->encodePassword($user, $user->getPassword()));
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $username = $user->getUsername();
            $email = (new TemplatedEmail())
                ->from(new Address('essaiphpmailer@gmail.com', 'Ne pas répondre'))
                ->to($user->getEmail())
                ->subject('Your password reset request')
                ->htmlTemplate('registration/email.html.twig')
                ->context([
                    'Token' => $confirmationToken,
                    'username' => $username,
                    'tokenLifetime' => 3600,
                ]);

                $mailer->send($email);

            return $this->respondWithSuccess(sprintf('Votre inscription a été validée, vous aller recevoir un email de confirmation pour activer votre compte et pouvoir vous connecté', $user->getUsername()));
        } else {
            //dd($form->getErrors(true));
            return $this->json((string) $form->getErrors(true), 400);
        }
    }


    /**
     * @Route("/account/confirm/{token}/{username}", name="confirm_account")
     * @param $token
     * @param $username
     * @return Response
     */
    public function confirmAccount($token, $username): Response
    {



        //dd($username, $token);

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->findOneBy(['username' => $username]);
        $tokenExist = $user->getConfirmationToken();
        if($token === $tokenExist) {
           $user->setConfirmationToken(null);
           $user->setIsActive(true);
           $em->persist($user);
           $em->flush();
        
           // TODO: Change Url
           //return $this->redirect('http://adressedusite.com');
           return $this->redirectToRoute('app_login');
        } else {
            return $this->render('registration/token-expire.html.twig');
        }
    }

    /**
     * @param UserInterface $user
     * @param JWTTokenManagerInterface $JWTManager
     * @return JsonResponse
     */
    public function getTokenUser(UserInterface $user, JWTTokenManagerInterface $JWTManager)
    {
        //dd("coucou");
        return new JsonResponse(['token' => $JWTManager->create($user)]);
    }


    /**
     * generate a token
     *
     * 
     */
    private function generateToken()
    {
        return rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
    }
}
