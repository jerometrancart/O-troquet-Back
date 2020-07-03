<?php

namespace App\Controller;

use App\Service\ImageUploader;
use App\Service\MailerService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
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
     * @Route("/admin", name="admin_index", methods={"GET"})
     */
    public function indexAdmin(UserRepository $userRepository): Response
    {
        return $this->render('admin/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }


    /**
     * @Route("/{id}/profil", name="user_profil", methods={"GET", "POST"})
     */
    public function profil(User $user): Response
    {
        return $this->render('user/profil.html.twig', [
            'user' => $user,
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

        $mailerService = new MailerService($mailer);
        // I get my entity back
        $user = $this->getDoctrine()->getRepository(User::class)->find($user);
        // I'm asking for the manager.
        $manager = $this->getDoctrine()->getManager();

        $user->setIsBanned(true);

        // I ask the manager to execute in the DB all the modifications that have been made on the entire database.tés
        $manager->flush();
        $to = $user->getEmail();

        
        $mailerService->sendToken($token = [], $to, $user,$tokenLifeTime = [],'Compte banni','user/banned.html.twig');




        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }
    /**
     * @Route("/{id}/unbanned", name="user_unbanned", methods={"GET","POST"})
     */
    public function unbanned($id)
    {
        // I get my entity back
        $user = $this->getDoctrine()->getRepository(User::class)->find($id);
        // I'm asking for the manager
        $manager = $this->getDoctrine()->getManager();

        $user->setIsBanned(false);
        // I ask the manager to execute in the database all the modifications that have been made on the entitie
        $manager->flush();

        return $this->redirectToRoute('user_index');
    }


    /**
     * @Route("/{id}/edit", name="user_edit", methods={"GET","POST"})
     */
    public function edit(UserPasswordEncoderInterface $passwordEncoder, Request $request, User $user): Response
    {
        if($user === $user){
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // We check if the password field contains a va
            // We retrieve the value of this field

            $password = $form->get('password')->getData();

            // If nothing has been typed in the password field, we get the null valu
            // If $password is different from null, we change the password of $user
            if ($password != null) {
                $encodedPassword = $passwordEncoder->encodePassword($user, $password);
                $user->setPassword($encodedPassword);
            }

            /** @var ImageUploader avatar */
            $avatar = $form->get('avatar')->getData();
            if ($avatar) {
                $filename = uniqid() . '.' . $avatar->guessExtension();

                $avatar->move(
                    $this->getParameter('avatar_directory'),
                    $filename
                );

                $user->setAvatar($filename);
            }
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('user_index');
        }else('vous ne pouvez pas modifié ce profil');
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }



}
