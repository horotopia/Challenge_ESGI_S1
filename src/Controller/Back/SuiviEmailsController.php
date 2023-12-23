<?php

namespace App\Controller\Back;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SuiviEmailsController extends AbstractController
{
    #[Route('/admin/suivi-emails', name: 'back_app_suivi_emails')]
    public function index(): Response
    {
        return $this->render('back/suivi-emails/index.html.twig', [
            'controller_name' => 'SuiviEmailsController',
        ]);
    }
}
