<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PaiementsController extends AbstractController
{
    #[Route('/admin/paiements', name: 'app_back_paiements')]
    public function index(): Response
    {
        return $this->render('back/paiements/index.html.twig', [
            'controller_name' => 'PaiementsController',
        ]);
    }
}
