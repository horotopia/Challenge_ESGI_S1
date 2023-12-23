<?php

namespace App\Controller\Back;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProduitController extends AbstractController
{
    #[Route('/produit', name: 'back_app_produit')]
    public function index(): Response
    {
        return $this->render('front/produit/index.html.twig', [
            'controller_name' => 'ProduitController',
        ]);
    }
}
