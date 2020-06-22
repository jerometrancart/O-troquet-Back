<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use App\Form\UserType;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/user")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/", name="user_index", methods={"GET"})
     */
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }
    /**
     * @Route("/{id}", name="user_show", methods={"GET"})
     */
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }
    /**
     * @Route("/{id}/banned", name="user_banned", methods={"GET","POST"})
     */
    public function banned(User $user, MailerInterface $mailer,UserRepository $userRepository)
    {
        // je recupère mon entité
        $user = $this->getDoctrine()->getRepository(User::class)->find($user);
        // je demande le manager
        $manager = $this->getDoctrine()->getManager();

        $user->setIsBanned(true);

        // je demande au manager d'executer dans la BDD toute les modifications qui ont été faites sur les entités
        $manager->flush();
        $email = (new TemplatedEmail())
        ->from('essaiphpmailer@gmail.com')
        ->to($user->getEmail())
        //->cc('cc@example.com')
        //->bcc('bcc@example.com')
        //->replyTo('fabien@example.com')
        //->priority(Email::PRIORITY_HIGH)
        ->subject('O\'troquet ')
        ->text('Sending emails is fun again!')
        ->htmlTemplate('user/banned.html.twig')
        ->context([
                'expiration_date' => new \DateTime('+7 days'),
                'username' => $user,
            ]);

            
        $mailer->send($email);


        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }
    /**
     * @Route("/{id}/unbanned", name="user_unbanned", methods={"GET","POST"})
     */
    public function unbanned($id)
    {
        // je recupère mon entité
        $user = $this->getDoctrine()->getRepository(User::class)->find($id);
        // je demande le manager
        $manager = $this->getDoctrine()->getManager();

        $user->setIsBanned(false);
        // je demande au manager d'executer dans la BDD toute les modifications qui ont été faites sur les entités
        $manager->flush();

        return $this->redirectToRoute('user_index');
    }

    /**
     * @Route("/{id}/edit", name="user_edit", methods={"GET","POST"})
     */
    public function edit(UserPasswordEncoderInterface $passwordEncoder, Request $request, User $user): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // On vérifie si le champs password contient une valeur
            // On récupère la valeur de ce champs
            $password = $form->get('password')->getData();

            // Si rien n'a été tapé dans le champs password, on reçoit las valeur null
            // Si $password est différent de null, on modifie le mot de passe de $user
            if ($password != null) {
                $encodedPassword = $passwordEncoder->encodePassword($user, $password);
                $user->setPassword($encodedPassword);
            }

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('user_index');
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

}
