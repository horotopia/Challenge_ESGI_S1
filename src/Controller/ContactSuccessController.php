<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContactSuccessController extends AbstractController
{
    #[Route('/contact/success', name: 'app_contact_success')]
    public function index(): Response
    {
        return $this->render('front/contact/success.html.twig', [
            'controller_name' => 'Formulaire bien envoyé',
        ]);
    }
}