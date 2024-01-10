<?php

namespace App\Controller;



use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PreviewEmailController extends AbstractController
{
    #[Route('/preview/email', name: 'app_preview_email')]
    public function index(Request $request): Response
    {
         $email=$request->query->get('email');
        return $this->render('front/preview_email/index.html.twig', [
            'email' => $email,
        ]);
    }
}
