<?php

namespace App\Controller;

use App\Repository\EmailLogRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
#[IsGranted('ROLE_ENTREPRISE')]
class EmailTrackingController extends AbstractController
{
    #[Route('/admin/email-tracking', name: 'app_back_email_tracking')]
    public function index(EmailLogRepository $emailLogRepository): Response {
        $logs = $emailLogRepository->findAll();

        return $this->render('back/email_tracking/index.html.twig', [
            'controller_name' => 'EmailTrackingController',
            'logs' => $logs,
        ]);
    }
}
