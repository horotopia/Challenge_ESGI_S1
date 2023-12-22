<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UtilisateursController extends AbstractController
{
    #[Route('/admin/utilisateurs', name: 'app_back_utilisateurs')]
    public function index(): Response
    {
        return $this->render('back/utilisateurs/index.html.twig', [
            'controller_name' => 'UtilisateursController',
        ]);
    }
}