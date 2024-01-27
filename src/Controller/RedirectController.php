<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RedirectController extends AbstractController
{

    #[Route('/home', name: 'home')]
    public function home(): Response
    {
        return $this->render('shared/welcome.html.twig', [
            'controller_name' => 'ManagementController',
        ]);
    }

    #[Route('/', name: 'redirect_home')]
    public function index(): Response
    {
        return $this->render('shared/welcome.html.twig', [
            'controller_name' => 'RedirectController',
        ]);
    }
}
