<?php

namespace App\Controller\Api\V1;



use App\Entity\Game;
use App\Repository\GameRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;


/**
 * @Route("/api/v1/game", name="api_v1_game_")
 */
class GameController extends AbstractController
{

    private $normalizer;

    public function __construct(ObjectNormalizer $objetNormalizer)
    {
        $this->normalizer = $objetNormalizer;
        $this->serializer = new Serializer([$objetNormalizer]);
    }

    /**
     * @Route("/", name="list")
     */
    public function list(GameRepository $gameRepository)
    {
        $game = $gameRepository->findAll();
        /// dd($game);


        $json = $this->serializer->normalize($game, null, ['groups' => 'api_v1_game']);

        return $this->json($json);
    }


    /**
     * @Route("/{id}", name="read", methods={"GET"})
     */
    public function read(Game $game)
    {
        return $this->json(
            $this->serializer->normalize($game, null, ['groups' => ['api_v1_game']]));
    }


}


