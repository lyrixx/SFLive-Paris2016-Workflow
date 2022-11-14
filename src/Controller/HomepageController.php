<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomepageController extends AbstractController
{
    #[Route(path: '/', name: 'homepage')]
    public function index(): Response
    {
        return $this->render('homepage/index.html.twig');
    }

    #[Route(path: '/login/{username}', name: 'login')]
    public function login(): never
    {
        throw new \LogicException('The security component should handle this route.');
    }

    #[Route(path: '/logout', name: 'logout')]
    public function logout(): never
    {
        throw new \LogicException('The security component should handle this route.');
    }
}
