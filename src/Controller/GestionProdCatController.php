<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
#[IsGranted('ROLE_ENTERPRISE')]
class GestionProdCatController extends AbstractController
{
    #[Route('/admin/gestion-prod-cat', name: 'app_back_gestion_prod_cat')]
    public function index(): Response
    {
        return $this->render('back/gestion-prod-cat/index.html.twig', [
            'controller_name' => 'GestionProdCatController',
        ]);
    }
}
