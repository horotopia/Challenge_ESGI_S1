<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ClientsController extends AbstractController
{
    #[Route('/admin/clients', name: 'app_back_clients')]
    public function index(): Response
    {
        return $this->render('back/clients/index.html.twig', [
            'controller_name' => 'ClientsController',
        ]);
    }
}
