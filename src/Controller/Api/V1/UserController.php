<?php

namespace App\Controller\Api\V1;



use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;


/**
 * @Route("/api/v1/users", name="api_v1_user_")
 */
class UserController extends AbstractController
{

    private $normalizer;
    private $encoder;
    public function __construct(ObjectNormalizer $objetNormalizer, UserPasswordEncoderInterface $encoder)
    {
        $this->normalizer = $objetNormalizer;
        $this->serializer = new Serializer([$objetNormalizer]);
        $this->encoder = $encoder;
    }

    /**
     *
     * @Route("/", name="list")
     *
     */
    public function list(UserRepository $userRepository)
    {
        $users = $userRepository->findAll();
        /// dd($users);


        $json = $this->serializer->normalize($users, null, ['groups' => 'api_v1_users']);

        return $this->json($json);
    }


    /**
     * @Route("/{id}", name="read", methods={"GET"})
     */
    public function read(User $user)
    {
        return $this->json(
            $this->serializer->normalize($user, null, ['groups' => ['api_v1_users']]));
    }


    /**
     * @Route("", name="new", methods={"POST"})
     */
    public function new(Request $request, ObjectNormalizer $objetNormalizer)
    {
        // Depuis l'installation du JWT, on peut retrouver l'utilisateur connecté
        // comme si on avait une session classique avec un cookie
        // Pour retrouver l'utilisateur :
        // $user = $this->getUser();
        // dd($user);
        // On pourrait par exemple vérifier le rôle de l'utilisateur ici
        // Encore mieux, on pourrait utiliser des Voters pour vérifier que cet utilisateur a le droir «add» sur $movie


        // Pour créer un nouveau Movie depuis une requête en API
        // on peut utiliser les formulaires
        // La structure des données permettra d'associer
        // les propriétés du JSON aux champs de notre formulaire

        $user = new User();

        // L'option supplémentaire permet de ne pas vérifier le token CSRF
        // Comme on est en API, les requêtes sont forcément forgées,
        // elles proviennent d'utilisateurs qu'on pourra identifier, la protection CSRF est injustifiée ici
        $form = $this->createForm(UserType::class, $user, ['csrf_protection' => false]);

        // L'option true (deuxième argument de json_decode(), permet de spécifier qu'on veut un arra yet pas un objet)
        $json = json_decode($request->getContent(), true);

        // On simule l'envoi du formulaire
        $form->submit($json);

        // On vérifie que les données reçues sont valides selon les contraintes de validation qu'on connait
        if ($form->isValid()) {

            // Ça y est, les données de la requête ont été associées à notre formulaire puis à $movie
            // Il ne reste plus qu'à persister $movie
            $user->setPassword($this->encoder->encodePassword($user, $user->getPassword()));
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            // Tout a bien fonctionné, on sérialise $movie pour l'afficher
            // Ça sert de confirmation
            $serializer = new Serializer([$objetNormalizer]);
            $userJson = $serializer->normalize($user, null, ['groups' => 'api_v1_users']);

            // On précise le code de status de réponse 201 Created
            return $this->json($userJson, 201);
        } else {
            // Si le formulaire n'est pas valide, on peut renvoyer les erreurs
            // Attention il s'agit d'une chaine de caractères qui n'explique pas grand chose,
            // Ce n'est pas du JSON, il y a sûrement un moyen, à la main, de sérialiser les erreurs mieux que ça
            // On précise également le code de status de réponse : 400
            // (string) c'est pour parser (transformer) notre objet en string
            ($form->getErrors(true));

            return $this->json((string)$form->getErrors(true), 400);
        }
    }




    /**
     * @Route("/{id}/update", name="update",  methods={"GET","POST"})
     */
    public function update(Request $request, User $user, ObjectNormalizer $objetNormalizer)
    {

        $form = $this->createForm(UserType::class, $user, ['csrf_protection' => false]);

        // L'option true (deuxième argument de json_decode(), permet de spécifier qu'on veut un arra yet pas un objet)
        $json = json_decode($request->getContent(), true);

        // On simule l'envoi du formulaire
        $form->submit($json);
        if ( $form->isValid()) {

            $user->setPassword($this->encoder->encodePassword($user, $user->getPassword()));
            $manager = $this->getDoctrine()->getManager();
            // Pas besoin de persist, l'objet manipulé est déjà connu du manager
            $manager->flush();
            $serializer = new Serializer([$objetNormalizer]);
            $userJson = $serializer->normalize($user, null, ['groups' => 'api_v1_users']);

            // On précise le code de status de réponse 201 Created
            return $this->json($userJson, 201);
        } else {
            // Si le formulaire n'est pas valide, on peut renvoyer les erreurs
            // Attention il s'agit d'une chaine de caractères qui n'explique pas grand chose,
            // Ce n'est pas du JSON, il y a sûrement un moyen, à la main, de sérialiser les erreurs mieux que ça
            // On précise également le code de status de réponse : 400
            // (string) c'est pour parser (transformer) notre objet en string
            ($form->getErrors(true));

            return $this->json((string)$form->getErrors(true), 400);

        }
    }


    /**
     * @Route("/{id}", name="delete", methods={"DELETE"})
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
        return $this->json([
            'message' => 'Vôtre compte a bien supprimé',
            'path' => 'src/Controller/Api/V1/UserController.php',
        ]);
    }






    /**
     * @Route("/{id}/banned", name="banned",methods={"GET","POST"})
     */
    public function banned($id)
    {
        // je recupère mon entité
        $user = $this->getDoctrine()->getRepository(User::class)->find($id);
        // je demande le manager
        $manager = $this->getDoctrine()->getManager();

        $user->setIsActive(false);
        // je demande au manager d'executer dans la BDD toute les modifications qui ont été faites sur les entités
        $manager->flush();
        return $this->json([
            'message' => 'Vôtre compte à été banni',
            'path' => 'src/Controller/Api/V1/UserController.php',
        ]);
    }

}


