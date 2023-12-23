<?php

namespace App\Controller\Front;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CGVController extends AbstractController
{
    #[Route('/conditions-generales-de-vente', name: 'front_app_cgv')]
    public function index(): Response
    {
        return $this->render('front/cgv/index.html.twig', [
            'controller_name' => 'CGVController',
        ]);
    }
}
