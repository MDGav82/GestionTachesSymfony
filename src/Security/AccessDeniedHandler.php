<?php
// src/Security/AccessDeniedHandler.php
namespace App\Security;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Http\Authorization\AccessDeniedHandlerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Twig\Environment;

class AccessDeniedHandler implements AccessDeniedHandlerInterface
{
    private RouterInterface $router;
    private Environment $twig;

    public function __construct(RouterInterface $router, Environment $twig)
    {
         $this->router = $router;
         $this->twig = $twig;
    }

    public function handle(Request $request, AccessDeniedException $accessDeniedException): ?Response
    {
         // Rendre une vue Twig personnalisée
         $content = $this->twig->render('security/access_denied.html.twig', [
             // Vous pouvez transmettre des variables à la vue si besoin
             'message' => 'Vous n\'avez pas les droits nécessaires pour accéder à cette page.'
         ]);

         return new Response($content, Response::HTTP_FORBIDDEN);
    }
}
