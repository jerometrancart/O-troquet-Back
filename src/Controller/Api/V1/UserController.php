<?php

namespace App\Controller\Api\V1;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;


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
        //$serializer = new Serializer([$this->objetNormalizer]);


        $json = $this->serializer->normalize($users, null, ['groups' => 'api_v1_users']);

        return $this->json($json);
    }


    /**
     * @Route("/{id}", name="read", methods={"GET"})
     */
    public function read(User $question)
    {
        return $this->json(
            $this->serializer->normalize(
                $question,
                null,
                [
                    'groups' =>
                    [
                        'api_v1_questions',
                        'api_v1_questions_details'

                    ],
                ]
            )
        );
    }
}


