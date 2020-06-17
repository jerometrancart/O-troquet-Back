<?php

/**
 * Created by PhpStorm.
 * User: hicham benkachoud
 * Date: 06/01/2020
 * Time: 20:39
 */

namespace App\Controller\Api\V1;


use App\Entity\User;
use App\Controller\Api\V1\ApiController;
use App\Form\RegistrationFormType;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class AuthController extends ApiController
{

    /**
     * 
     * @Route("/api/register", name="api_register", methods={"POST"})
     * 
     */
    public function register(Request $request, UserPasswordEncoderInterface $encoder)
    {


        $user = new User;
        $form = $this->createForm(RegistrationFormType::class, $user, ['csrf_protection' => false]);

        $json = json_decode($request->getContent(), true);

        $form->submit($json);

        if ($form->isValid()) {
           
            
            $user->setPassword($encoder->encodePassword($user, $user->getPassword()));
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            return $this->respondWithSuccess(sprintf('User %s successfully created', $user->getUsername()));
        } else {
            dd($form->getErrors(true));
            return $this->json((string) $form->getErrors(true), 400);
        }
    }

    /**
     * @param UserInterface $user
     * @param JWTTokenManagerInterface $JWTManager
     * @return JsonResponse
     */
    public function getTokenUser(UserInterface $user, JWTTokenManagerInterface $JWTManager)
    {
        return new JsonResponse(['token' => $JWTManager->create($user)]);
    }
}
