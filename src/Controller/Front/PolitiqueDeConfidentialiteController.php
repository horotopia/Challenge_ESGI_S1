<?php

namespace App\Controller\Front;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PolitiqueDeConfidentialiteController extends AbstractController
{
    #[Route('/politique-de-confidentialite', name: 'front_app_politique_de_confidentialite')]
    public function index(): Response
    {
        return $this->render('front/politique_de_confidentialite/index.html.twig', [
            'controller_name' => 'PolitiqueDeConfidentialiteController',
        ]);
    }
}
