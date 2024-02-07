<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
#[IsGranted('ROLE_ENTERPRISE')]
class PaymentsController extends AbstractController
{
    #[Route('/admin/payments', name: 'app_back_payments')]
    public function index(): Response
    {
        return $this->render('back/payments/index.html.twig', [
            'controller_name' => 'PaymentsController',
        ]);
    }
}
