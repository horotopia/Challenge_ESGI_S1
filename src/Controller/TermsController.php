<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TermsController extends AbstractController
{
    #[Route('/terms-and-conditions', name: 'app_terms_and_conditions')]
    public function index(): Response
    {
        return $this->render('front/termes/index.html.twig', [
            'controller_name' => 'TermsController',
        ]);
    }
}
