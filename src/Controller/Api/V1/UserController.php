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
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/api/v1/users", name="api_v1_user_")
 */
class UserController extends AbstractController
{

    private $objetNormalizer;
    private $encoder;

    public function __construct(ObjectNormalizer $objetNormalizer,UserPasswordEncoderInterface $encoder)
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

        $json = $this->serializer->normalize($users, null, ['groups' => 'api_v1_users']);

        return $this->json($json);
    }

    /**
     * @Route("/{id}", name="read", methods={"GET"})
     */
    public function read(User $user)
    {
        return $this->json(
            $this->serializer->normalize($user, null, ['groups' => ['api_v1_users','api_v1_users_read']])
        );
    }


    /**
     * @Route("", name="add", methods={"POST"})
     */
    public function add(Request $request, ObjectNormalizer $objetNormalizer)
    {

        $user = new User();

        $form = $this->createForm(UserType::class, $user, ['csrf_protection' => false]);

        // L'option true (deuxième argument de json_decode(), permet de spécifier qu'on veut un arra yet pas un objet)
        $json = json_decode($request->getContent(), true);
        //dd($json);
        // On simule l'envoi du formulaire
        $form->submit($json);
        //dd($form->submit($json));

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
            dd($form->getErrors(true));

            return $this->json((string) $form->getErrors(true), 400);
        }
    }
}
