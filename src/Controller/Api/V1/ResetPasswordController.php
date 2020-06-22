<?php

namespace App\Controller\Api\V1;

use App\Controller\Api\V1\ApiController;
use App\Entity\User;
use App\Form\ChangePasswordFormType;
use App\Form\ResetPasswordRequestFormType;
use App\Service\MailerService;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use SymfonyCasts\Bundle\ResetPassword\Controller\ResetPasswordControllerTrait;
use SymfonyCasts\Bundle\ResetPassword\Exception\ResetPasswordExceptionInterface;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;

/**
 * @Route("api/reset-password")
 * 
 */
class ResetPasswordController extends ApiController
{
    use ResetPasswordControllerTrait;

    private $resetPasswordHelper;

    public function __construct(ResetPasswordHelperInterface $resetPasswordHelper)
    {
        $this->resetPasswordHelper = $resetPasswordHelper;
    }

    /**
     * Display & process form to request a password reset.
     *
     * @Route("", name="app_forgot_password_request")
     */
    public function request(Request $request, MailerInterface $mailer): Response
    {

       // $user = new User;

        $form = $this->createForm(ResetPasswordRequestFormType::class, null,['csrf_protection' => false]);
       // $form->handleRequest($request);;
       $json = json_decode($request->getContent(),true);


    
       $form->submit($json);

        //dd($json);
        if ($form->isValid()) {
           //dd($form->get('email')->getData());
            return $this->processSendingPasswordResetEmail(
                $form->get('email')->getData(),
                $mailer,
            );
        } else {
           // dd($form->getErrors());
            return $this->json((string) $form->getErrors(true), 400);
        }

    }

    /**
     * Confirmation page after a user has requested a password reset.
     *
     * @Route("/check-email", name="app_check_email")
     */
    public function checkEmail(): Response
    {
      
        // We prevent users from directly accessing this page
        if (!$this->canCheckEmail()) {
            return $this->redirectToRoute('app_forgot_password_request');
        }

        return $this->respondWithSuccess(sprintf('Un email contenant le lien pour mofifier votre mot de passe vous a été envoyé, il expirera dans %s heure',2));
    }

    /**
     * Validates and process the reset URL that the user clicked in their email.
     *
     * @Route("/reset/{token}", name="app_reset_password")
     */
    public function reset(Request $request, UserPasswordEncoderInterface $passwordEncoder, string $token = null): Response
    {
        if ($token) {
            // We store the token in session and remove it from the URL, to avoid the URL being
            // loaded in a browser and potentially leaking the token to 3rd party JavaScript.
            $this->storeTokenInSession($token);

            return $this->redirectToRoute('app_reset_password');
        }

        $token = $this->getTokenFromSession();
        if (null === $token) {
            throw $this->createNotFoundException('No reset password token found in the URL or in the session.');
        }

        try {
            $user = $this->resetPasswordHelper->validateTokenAndFetchUser($token);
        } catch (ResetPasswordExceptionInterface $e) {
            $this->addFlash('reset_password_error', sprintf(
                'There was a problem validating your reset request - %s',
                $e->getReason()
            ));

            return $this->redirectToRoute('app_forgot_password_request');
        }

        // The token is valid; allow the user to change their password.
        $form = $this->createForm(ChangePasswordFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // A password reset token should be used only once, remove it.
            $this->resetPasswordHelper->removeResetRequest($token);

            // Encode the plain password, and set it.
            $encodedPassword = $passwordEncoder->encodePassword(
                $user,
                $form->get('plainPassword')->getData()
            );

            $user->setPassword($encodedPassword);
            $this->getDoctrine()->getManager()->flush();

            // The session is cleaned up after the password has been changed.
            $this->cleanSessionAfterReset();


            //return $this->redirect('http://adressedusite.com');
            return $this->redirectToRoute('homepage');
        }

        return $this->render('reset_password/reset.html.twig', [
            'resetForm' => $form->createView(),
        ]);
    }

    private function processSendingPasswordResetEmail(string $emailFormData, MailerInterface $mailer)
    {
     
    
        $mailerService = new MailerService($mailer);
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy([
            'email' => $emailFormData,
        ]);
        // Marks that you are allowed to see the app_check_email page.
        $this->setCanCheckEmailInSession();

        // Do not reveal whether a user account was found or not.
        if (!$user) {
            return $this->redirectToRoute('app_check_email');
        }
        
        try {
           
            $resetToken = $this->resetPasswordHelper->generateResetToken($user);

        } catch (ResetPasswordExceptionInterface $e) {
            return $this->json(sprintf(
                'There was a problem handling your password reset request - %s',
                $e->getReason()
            ));
                
        }
       

        $email = $user->getEmail();
        $username = $user->getUsername(); 
        $tokenLifeTime = $this->resetPasswordHelper->getTokenLifetime();
        $tokenLifeTimeInHour = ($tokenLifeTime / 3600);
      

        $mailerService->sendToken($resetToken,$email,$username,$tokenLifeTime,'Your password reset request','reset_password/email.html.twig');
    

        return $this->respondWithSuccess(sprintf('Un email contenant le lien pour mofifier votre mot de passe vous a été envoyé, il expirera dans %s heure',$tokenLifeTimeInHour));

        
    }
}
