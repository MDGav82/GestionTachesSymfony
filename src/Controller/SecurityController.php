<?php

namespace App\Controller;

use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils, Request $request, SessionInterface $session): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        // $error = $authenticationUtils->getLastAuthenticationError();
        // $lastUsername = $authenticationUtils->getLastUsername();

        // // Stocker les données en session
        // $session->set('last_username', $lastUsername);
        // $session->set('error', $error ? $error->getMessage() : null);
        if ($lastUsername != "") {

            return $this->redirectToRoute('app_project_index');

        } else {
            return $this->render('security/login.html.twig', [
                'error' => $error,
                'last_username' => $lastUsername

            ]);
        }
    }

    // #[Route(path: '/logged', name: 'app_logged')]
    // public function logged(AuthenticationUtils $authenticationUtils, LoggerInterface $logger, Request $request, SessionInterface $session): Response
    // {

    //     // Récupérer l'erreur d'authentification s'il y en a une

    //     if (isset($lastUsername) && $lastUsername != "") {
    //         return $this->redirectToRoute('app_project_index');
    //     } else {
    //         return $this->redirectToRoute('app_login');
    //     }
    // }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {

        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
