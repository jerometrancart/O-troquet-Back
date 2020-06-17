<?php

namespace App\Controller\Api\V1;


use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;


/**
 * @Route("/api/v1/users", name="api_v1_user_")
 */
class UserController extends AbstractController
{

    private $normalizer;

    public function __construct(ObjectNormalizer $objetNormalizer)
    {
        $this->normalizer = $objetNormalizer;
        $this->serializer = new Serializer([$objetNormalizer]);
    }
   /**
     * 
     * @Route("/", name="list")
     * 
     */
    public function list( UserRepository $userRepository)
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
            $this->serializer->normalize($user,null,['groups' =>['api_v1_users']]));
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
            //dd($form->getErrors(true));
            return $this->json((string) $form->getErrors(true), 400);
        }
    } 

}


