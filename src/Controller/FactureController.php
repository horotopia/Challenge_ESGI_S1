<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
#[IsGranted('ROLE_ENTERPRISE')]
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
