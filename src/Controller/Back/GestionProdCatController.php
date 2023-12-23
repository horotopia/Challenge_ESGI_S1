<?php

namespace App\Controller\Back;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GestionProdCatController extends AbstractController
{
    #[Route('/admin/gestion-prod-cat', name: 'back_app_gestion_prod_cat')]
    public function index(): Response
    {
        return $this->render('back/gestion-prod-cat/index.html.twig', [
            'controller_name' => 'GestionProdCatController',
        ]);
    }
}
