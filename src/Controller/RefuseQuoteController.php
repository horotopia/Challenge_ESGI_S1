<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RefuseQuoteController extends AbstractController
{
    #[Route('/refuse/quote', name: 'app_refuse_quote')]
    public function index(): Response
    {
        return $this->render('front/refuse_quote/index.html.twig', [
            'controller_name' => 'Refus de devis',
        ]);
    }
}
