<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LegalNoticesController extends AbstractController
{
    #[Route('/legal-notices', name: 'app_legal_notices')]
    public function index(): Response
    {
        return $this->render('front/legales_notices/index.html.twig', [
            'controller_name' => 'LegalNoticesController',
        ]);
    }
}
