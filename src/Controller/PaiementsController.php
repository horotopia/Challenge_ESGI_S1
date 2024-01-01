<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
#[IsGranted('ROLE_ENTERPRISE')]
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
