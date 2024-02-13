<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AcceptQuoteController extends AbstractController
{
    #[Route('/accept/quote', name: 'app_accept_quote')]
    public function index(): Response
    {
        return $this->render('front/accept_quote/index.html.twig', [
            'controller_name' => 'Accepter le devis',
        ]);
    }
}
