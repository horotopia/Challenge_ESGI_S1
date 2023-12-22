<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FactureController extends AbstractController
{
    #[Route('/admin/facture', name: 'app_back_facture')]
    public function index(): Response
    {
        return $this->render('back/facture/index.html.twig', [
            'controller_name' => 'FactureController',
        ]);
    }
}
