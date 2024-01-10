<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
#[IsGranted('ROLE_ENTERPRISE')]
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
