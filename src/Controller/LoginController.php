<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{
    #[Route('/login', name: 'login')]
    public function index(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        if ($error == null)
        {
            return $this->render('login/index.html.twig', [
                'controller_name' => 'LoginController',
                'last_username' => $lastUsername,
                'error' => $error,
            ]);
        }
        else
        {
            return $this->redirectToRoute('app_register', [
                'controller_name' => 'LoginController',
                'last_username' => $lastUsername,
                'error' => $error,
            ]);
        }


    }


}
