<?php

namespace App\Controller\Front;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TarifsController extends AbstractController
{
    #[Route('/tarifs', name: 'front_app_tarifs')]
    public function index(): Response
    {
        return $this->render('front/tarifs/index.html.twig', [
            'controller_name' => 'TarifsController',
        ]);
    }
}
