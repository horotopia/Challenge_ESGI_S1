<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
#[IsGranted('ROLE_ENTERPRISE')]
class DevisController extends AbstractController
{
    #[Route('/admin/devis', name: 'app_back_devis')]
    public function index(): Response
    {
        return $this->render('back/devis/index.html.twig', [
            'controller_name' => 'DevisController',
        ]);
    }
}
