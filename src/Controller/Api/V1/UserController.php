<?php

namespace App\Controller\Api\V1;

use App\Entity\Play;
use App\Entity\User;
use App\Entity\UserFriends;
use App\Form\UserType;
use App\Repository\UserRepository;
use App\Repository\PlayRepository;
use App\Repository\UserFriendsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ObjectManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;


/**
 * @Route("/api/v1/users", name="api_v1_user_")
 */
class UserController extends ApiController
{
    private $objetNormalizer;
    private $encoder;

    public function __construct(ObjectNormalizer $objetNormalizer, UserPasswordEncoderInterface $encoder)
    {
        $this->normalizer = $objetNormalizer;
        $this->serializer = new Serializer([$objetNormalizer]);
        $this->encoder = $encoder;
    }



    /**
     *
     * add friends 
     * @Route("/{id}/requests/{idFriend}/friends", requirements={"id" = "\d+","id2" = "\d+"}, name="friendRequest")
     * 
     */
    public function friendRequest(User $user, $idFriend)
    {

        // check if the user is the one who sends friend's request
        if ($this->getUser()->getId() !== $user->getId()) {
            return $this->respondUnauthorized("t'as rien à faire là mon pote");
        }

        $friendship = $this->getDoctrine()->getRepository(UserFriends::class)->getFriendship($user, $idFriend);

        //check if the relation does not exist yet 
        if ($friendship !== null) {
            return $this->respondUnauthorized("Demande d'ami déja envoyée");
        }
        $friend = $this->getDoctrine()->getRepository(User::class)->find($idFriend);
        $manager = $this->getDoctrine()->getManager();

        //add first lign for friend relation
        $addNewRelation = new UserFriends;
        $addNewRelation->setUser($user);
        $addNewRelation->setFriend($friend);
        $addNewRelation->setIsAccepted(false);
        $addNewRelation->setIsAnswered(false);
        $manager->persist($addNewRelation);
        $manager->flush();
        return $this->respondCreated([
            'message' => sprintf('demande d\'ami envoyée à %s ', $friend->getUsername())
        ]);
    }

    /**
     *
     * add friends 
     * @Route("/{id}/response/{idFriend}/friends/{bool}", requirements={"id" = "\d+","id2" = "\d+","bool" = "[01]"}, name="friendResponse")
     * 
     */
    public function friendResponse(User $user, $idFriend, $bool)
    {
        // check if the user is the one who requests friend's request
        if ($this->getUser()->getId() !== $user->getId()) {
            return $this->respondUnauthorized("t'as rien à faire là mon pote");
        }

        $friend = $this->getDoctrine()->getRepository(User::class)->find($idFriend);
        $friendship = $this->getDoctrine()->getRepository(UserFriends::class)->getFriendship($idFriend, $user);

        //check if the first relation ($user -> $friend) exists (second security, may i delete later)
        if ($friendship == null) {
            return $this->respondUnauthorized("cette relation n'existe pas");
        };

        //check if the second relation ($friend -> $user ) does not exist
        $friendshipReverse = $this->getDoctrine()->getRepository(UserFriends::class)->getFriendship($user, $idFriend);


        if ($friendshipReverse !== null) {
            return $this->respondUnauthorized("Vous avez déjà répondu à cette invitation");
        };
        //add second lign for same relationship 
        $addNewRelation = new UserFriends;
        $addNewRelation->setUser($user);
        $addNewRelation->setFriend($friend);
        $addNewRelation->setIsAnswered(true);
        // and modify the first one
        $friendship->setIsAnswered(true);
        if ($bool == 1) {
            $friendship->setIsAccepted(true);
            $addNewRelation->setIsAccepted(true);
        } else {
            $friendship->setIsAccepted(false);
            $addNewRelation->setIsAccepted(false);
        }
        //Get Manager
        $manager = $this->getDoctrine()->getManager();
        $manager->persist($addNewRelation, $friendship);
        $manager->flush();

        $friendUsername =  $friend->getUsername();

        if ($bool == 1) {
            return $this->respondCreated([
                'message' => sprintf('vous êtes désormais ami(e) avec %s', $friendUsername)
            ], 201);
        } else {
            return $this->respondCreated([
                'message' => sprintf('vous avez décliné à la demande d\'ami de %s', $friendUsername)
            ], 201);
        }
    }


    /**
     *
     * add friends 
     * @Route("/{id}/unfriend/{idFriend}", requirements={"id" = "\d+","id2" = "\d+"}, name="unfriend")
     * 
     */
    public function unfriend(User $user, $idFriend)
    {


        // check if the user is the one who sends the unfriend  
        if ($this->getUser()->getId() !== $user->getId()) {
            return $this->respondUnauthorized("t'as rien à faire là mon pote");
        }

        $friendship = $this->getDoctrine()->getRepository(UserFriends::class)->getFriendship($user, $idFriend);
        $friendshipReverse = $this->getDoctrine()->getRepository(UserFriends::class)->getFriendship($idFriend, $user);

        if ($friendship === null) {
            return $this->respondUnauthorized("Cette relation n'existe pas");
        };


        //dd($friendship,$friendshipReverse);
        $manager = $this->getDoctrine()->getManager();
        $manager->remove($friendship);
        if ($friendshipReverse !== null) {
            $manager->remove($friendshipReverse);
        }
        $manager->flush();

        $friend = $this->getDoctrine()->getRepository(User::class)->find($idFriend);

        return $this->respondCreated([
            'message' => sprintf('Vous avez supprimé %s de votre liste d\'amis', $friend->getUsername())
        ], 201);
    }





    /**
     *
     * @Route("/", name="list")
     *
     */
    public function list(UserRepository $userRepository)
    {
        $users = $userRepository->findAll();
        $json = $this->serializer->normalize($users, null, ['groups' => 'api_v1_users']);
        return $this->json($json);
    }

    /**
     * @Route("/{id}", name="read", methods={"GET"})
     * 
     */
    public function read(int $id, Request $request, UserRepository $userRepository)
    {

        $user = $userRepository->getFullUser($id);


        return $this->respondWithSuccess(
            $this->serializer->normalize($user, 'null', ['groups' => ['api_v1_users', 'api_v1_users_read']])
        );
    }

    /**
     * @Route("", name="add", methods={"POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function new(Request $request, ObjectNormalizer $objetNormalizer)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user, ['csrf_protection' => false]);
        $json = json_decode($request->getContent(), true);
        $form->submit($json);

        if ($form->isValid()) {

            $user->setPassword($this->encoder->encodePassword($user, $user->getPassword()));
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $serializer = new Serializer([$objetNormalizer]);
            $userJson = $serializer->normalize($user, null, ['groups' => 'api_v1_users']);

            return $this->json($userJson, 201);
        } else {
            ($form->getErrors(true));
            return $this->respondWithErrors((string) $form->getErrors(true), 400);
        }
    }


    /**
     * @Route("/{id}/update", name="update",  methods={"GET","POST"})
     * 
     */
    public function update(Request $request, User $user, ObjectNormalizer $objetNormalizer)
    {

        $form = $this->createForm(UserType::class, $user, ['csrf_protection' => false]);

        // L'option true (deuxième argument de json_decode(), permet de spécifier qu'on veut un arra yet pas un objet)
        $json = json_decode($request->getContent(), true);

        // On simule l'envoi du formulaire
        $form->submit($json);
        if ($form->isValid()) {
            $user->setPassword($this->encoder->encodePassword($user, $user->getPassword()));
            $manager = $this->getDoctrine()->getManager();


            $manager->flush();
            $serializer = new Serializer([$objetNormalizer]);
            $userJson = $serializer->normalize($user, null, ['groups' => 'api_v1_users']);
            return $this->respondWithSuccess($userJson, 201);
        } else {

            ($form->getErrors(true));

            return $this->respondWithErrors((string) $form->getErrors(true), 400);
        }
    }

    /**
     * @Route("/{id}/stats", name="stats", methods={"GET"})
     */
    public function stats(User $user, Request $request)
    {
        return $this->json(
            $this->serializer->respondWithSuccess($user, null, ['groups' => ['api_v1_users_stat']])
        );
    }


    /**
     * @Route("/{id}", name="delete", methods={"DELETE"})
     * 
     */
    public function delete($id)
    {
        // je recupère mon entité
        $user = $this->getDoctrine()->getRepository(User::class)->find($id);
        // je demande le manager
        $manager = $this->getDoctrine()->getManager();
        // je dit au manager que cette entité devra faire l'objet d'une suppression
        $manager->remove($user);
        // je demande au manager d'executer dans la BDD toute les modifications qui ont été faites sur les entités
        $manager->flush();
        return $this->respondWithSuccess([
            'message' => 'Votre compte a bien supprimé',
        ]);
    }


    /**
     * @Route("/{id}/banned", name="banned",methods={"GET","POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    // For V2 
    public function banned($id)
    {
        $user = $this->getDoctrine()->getRepository(User::class)->find($id);

        $manager = $this->getDoctrine()->getManager();

        $user->setIsActive(false);
        $manager->flush();
        return $this->respondWithSuccess([
            'message' => 'Le compte à été banni',
        ]);
    }
}
