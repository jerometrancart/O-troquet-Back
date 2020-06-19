<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

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
        if($form->isSubmitted() && $form->isValid()) {
            // On vérifie si le champs password contient une valeur
            // On récupère la valeur de ce champs
            $password = $form->get('password')->getData();

            // Si rien n'a été tapé dans le champs password, on reçoit las valeur null
            // Si $password est différent de null, on modifie le mot de passe de $user
            //Permet d'enconder le mot de passe modifé
            if ($password != null) {
                $encodedPassword = $passwordEncoder->encodePassword($user, $password);
                $user->setPassword($encodedPassword);
                $manager = $this->getDoctrine()->getManager();
                // Pas besoin de persist, l'objet manipulé est déjà connu du manager
                $manager->flush();
                $this->addFlash("success", "Votre compte a été mise à jour");
                //On se redirige vers la catégorie modifié
                return $this->redirectToRoute('homepage', ['id' => $user->getId()]);
            }
        }

        return $this->render('security/update.html.twig', [
            "UserForm" => $form->createView(),
            'user' => $user,

        ]);

    }

    /**
     * Ici on demande en parametre de notre methode de controller un objet de type Category
     * Catregory etant une entité, Doctrine va essayer d'utiliser les parametres de la route pour retrouver l'entité Category correspondant a l'id passé dans la route
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
