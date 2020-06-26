<?php

namespace App\Controller\Api\V1;



use App\Entity\Game;
use App\Entity\Play;
use App\Entity\User;
use App\Repository\PlayRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;


/**
 * @Route("/api/v1/play", name="api_v1_game_")
 */
class PlayController extends ApiController
{

    private $normalizer;

    public function __construct(ObjectNormalizer $objetNormalizer)
    {
        $this->normalizer = $objetNormalizer;
        $this->serializer = new Serializer([$objetNormalizer]);
    }


    /**
     * 
     * 
     * @Route("/add", name="AddPlay",  methods={"POST"} )
     */
    public function add(PlayRepository $playRepository, Request $request)
    {
        $json = json_decode($request->getContent(), true);
    
        $play = new Play;
        $user = $this->getDoctrine()->getRepository(User::class)->find($json['user_id']);
        $game = $this->getDoctrine()->getRepository(Game::class)->find($json['game_id']);

        $play->setUser($user);
        $play->setGame($game);
        $play->setWin($json['win']);

        $em = $this->getDoctrine()->getManager();
        $em->persist($play);
        $em->flush();
      
        //  $json = $this->serializer->normalize($play, null, ['groups' => 'api_v1_game']);

      return $this->respondCreated();
    
    }


    /**
     * @Route("/", name="list")
     */
    public function list(PlayRepository $playRepository)
    {
        $play = $playRepository->findAll();
       // dd($play);


        $json = $this->serializer->normalize($play, null, ['groups' => 'api_v1_play']);

        return $this->json($json);
    }

}


