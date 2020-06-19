<?php

namespace App\Controller;

use App\Entity\Game;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/game")
 */
class GameController extends AbstractController
{
    /**
     * @Route("/list", name="game_list", methods={"GET"})
     */
    public function list(Request $request) {



        $games = $this->getDoctrine()->getRepository(Game::class)->findAll();

        return $this->render('game/list.html.twig', [
            "games" => $games,


        ]);
    }


    /**
     *
     * @Route("/{id}/view", name="game_view", requirements={"id" = "\d+"}, methods={"GET"})
     */
    public function viewGame(Game $game)
    {
        return $this->render('game/view.html.twig', [
            'game' => $game,
        ]);
    }
}
