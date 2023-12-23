<?php

namespace App\Controller\Back;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FactureController extends AbstractController
{
    #[Route('/admin/facture', name: 'back_app_facture')]
    public function index(): Response
    {
        return $this->render('back/facture/index.html.twig', [
            'controller_name' => 'FactureController',
        ]);
    }
}
