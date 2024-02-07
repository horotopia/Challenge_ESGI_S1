<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PricingController extends AbstractController
{
    #[Route('/prices', name: 'app_prices')]
    public function index(): Response
    {
        return $this->render('front/prices/index.html.twig', [
            'controller_name' => 'PricingController',
        ]);
    }
}
