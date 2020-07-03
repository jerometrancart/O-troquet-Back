<?php

namespace App\Controller;

use App\Entity\Achievement;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @Route("/achievement")
 */

class AchievementController extends AbstractController
{
    /**
     * @Route("/list", name="achievement_list", methods={"GET"})
     */
    public function list(Request $request) {

        
        $achievements = $this->getDoctrine()->getRepository(Achievement::class)->findAll();

        return $this->render('achievement/list.html.twig', [
            "achievements" => $achievements,


        ]);
    }

    /**
     *
     * @Route("/{id}/view", name="achievement_view", requirements={"id" = "\d+"}, methods={"GET"})
     */
    public function viewAchievement(Achievement $achievement)
    {
        return $this->render('achievement/view.html.twig', [
            'achievement' => $achievement,
        ]);
    }
}
