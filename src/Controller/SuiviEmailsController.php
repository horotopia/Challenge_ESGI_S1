<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SuiviEmailsController extends AbstractController
{
    #[Route('/admin/suivi-emails', name: 'app_back_suivi_emails')]
    public function index(): Response
    {
        return $this->render('back/suivi-emails/index.html.twig', [
            'controller_name' => 'SuiviEmailsController',
        ]);
    }
}
