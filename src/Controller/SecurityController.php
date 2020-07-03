<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;


class SecurityController extends AbstractController
{

    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('target_path');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();
    
    
        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
    
    /**
     * @Route("/{id}/update", name="security_update", requirements={"id" = "\d+"}, methods={"GET", "POST"})
     */
    public function update(Request $request, User $user, UserPasswordEncoderInterface $passwordEncoder): Response
    {

        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // We check if the password field contains a value
            // We retrieve the value of this field
            $password = $form->get('password')->getData();

            // If nothing has been typed in the password field, we get the null value.
            // If $password is different from null, we change the password of $user
            // Allows to embed the modified password
            if ($password != null) {
                $encodedPassword = $passwordEncoder->encodePassword($user, $password);
                $user->setPassword($encodedPassword);
                $manager = $this->getDoctrine()->getManager();
                // No need to persist, the manipulated object is already known to the manager.
                $manager->flush();
                $this->addFlash("success", "Votre compte a été mise à jour");
                //We're redirecting to the modified category
                return $this->redirectToRoute('homepage', ['id' => $user->getId()]);
            }
        }

        return $this->render('security/update.html.twig', [
            "UserForm" => $form->createView(),
            'user' => $user,

        ]);
    }

    /**
     *
     * @Route("/{id}/view", name="security_view", requirements={"id" = "\d+"}, methods={"GET"})
     */
    public function view(User $user)
    {
        return $this->render('security/view.html.twig', [
            'user' => $user,
        ]);
    }
}
