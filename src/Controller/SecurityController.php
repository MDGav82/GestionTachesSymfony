<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class SecurityController extends AbstractController
{
    #[Route(path: '/', name: 'app_homepage')]
    public function index(): RedirectResponse
    {
        return $this->redirectToRoute('app_login');
    }

    #[Route(path: '/login', name: 'app_login')]
    public function login(SessionInterface $sessionInterface,AuthenticationUtils $authenticationUtils, Request $request, UserRepository $userRepository): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        $user = $userRepository->findOneBy(['email' => $lastUsername]);
        
        // $error = $authenticationUtils->getLastAuthenticationError();
        // $lastUsername = $authenticationUtils->getLastUsername();

        // // Stocker les données en session
        // $session->set('last_username', $lastUsername);
        // $session->set('error', $error ? $error->getMessage() : null);
        if ($user != null ) {
         
            return $this->redirectToRoute('app_project_index');

        } else {
            return $this->render('security/login.html.twig', [
                'error' => $error,
                'last_username' => $lastUsername

            ]);
        }
    }

    #[Route('/register', name: 'app_register')]
    public function register(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = new User();

        // Création du formulaire d'inscription
        $form = $this->createFormBuilder($user)
            ->add('firstname', TextType::class, [
                'label' => 'Prénom'
            ])
            ->add('lastname', TextType::class, [
                'label' => 'Nom'
            ])
            ->add('email', EmailType::class, [
                'label' => 'Adresse email'
            ])
            ->add('phone_number', TextType::class, [
                'label' => 'Téléphone'
            ])
            ->add('password', PasswordType::class, [
                'label' => 'Mot de passe'
            ])
            ->add('register', SubmitType::class, [
                'label' => 'S\'inscrire'
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupération et hashage du mot de passe
            $plaintextPassword = $form->get('password')->getData();
            $hashedPassword = $passwordHasher->hashPassword($user, $plaintextPassword);
            $user->setPassword($hashedPassword);

            // Persistance en base de données
            $entityManager->persist($user);
            $entityManager->flush();

            // Redirection vers la page de login (ou une autre page)
            return $this->redirectToRoute('app_logout');
        }

        return $this->render('security/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
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
