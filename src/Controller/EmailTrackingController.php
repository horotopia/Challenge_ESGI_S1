<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
#[IsGranted('ROLE_ENTERPRISE')]
class EmailTrackingController extends AbstractController
{
    #[Route('/admin/email-tracking', name: 'app_back_email_tracking')]
    public function index(): Response
    {
        return $this->render('back/email_tracking/index.html.twig', [
            'controller_name' => 'EmailTrackingController',
        ]);
    }
}
