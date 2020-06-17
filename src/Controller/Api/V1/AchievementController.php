<?php

namespace App\Controller\Api\V1;



use App\Entity\Achievement;
use App\Form\AchievementType;
use App\Repository\AchievementRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;


/**
 * @Route("/api/v1/achievement", name="api_v1_achievement_")
 */
class AchievementController extends AbstractController
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
    public function list(AchievementRepository $achievementRepository)
    {
        $achievement = $achievementRepository->findAll();
        /// dd($achievement);


        $json = $this->serializer->normalize($achievement, null, ['groups' => 'api_v1_achievement']);

        return $this->json($json);
    }


    /**
     * @Route("/{id}", name="read", methods={"GET"})
     */
    public function read(Achievement $achievement)
    {
        return $this->json(
            $this->serializer->normalize($achievement, null, ['groups' => ['api_v1_achievement']]));
    }





    /**
     * @Route("/{id}/update", name="update",  methods={"GET","POST"})
     */
    public function update(Request $request, Achievement $achievement, ObjectNormalizer $objetNormalizer)
    {

        $form = $this->createForm(AchievementType::class, $achievement, ['csrf_protection' => false]);

        // L'option true (deuxième argument de json_decode(), permet de spécifier qu'on veut un arra yet pas un objet)
        $json = json_decode($request->getContent(), true);

        // On simule l'envoi du formulaire
        $form->submit($json);
        if ( $form->isValid()) {
            //$user->setUpdatedAt(new \DateTime());

            $manager = $this->getDoctrine()->getManager();
            // Pas besoin de persist, l'objet manipulé est déjà connu du manager
            $manager->flush();
            $serializer = new Serializer([$objetNormalizer]);
            $userJson = $serializer->normalize($achievement, null, ['groups' => 'api_v1_achievement']);

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
        $user = $this->getDoctrine()->getRepository(Achievement::class)->find($id);
        // je demande le manager
        $manager = $this->getDoctrine()->getManager();
        // je dit au manager que cette entité devra faire l'objet d'une suppression
        $manager->remove($user);
        // je demande au manager d'executer dans la BDD toute les modifications qui ont été faites sur les entités
        $manager->flush();
        return $this->json([
            'message' => 'Achievement supprimé',
            'path' => 'src/Controller/Api/V1/AchievementController.php',
        ]);
    }





}


