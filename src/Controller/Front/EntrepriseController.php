<?php

namespace App\Controller\Front;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EntrepriseController extends AbstractController
{
    #[Route('/entreprise', name: 'front_app_entreprise')]
    public function index(): Response
    {
        return $this->render('front/entreprise/index.html.twig', [
            'controller_name' => 'EntrepriseController',
        ]);
    }
}
