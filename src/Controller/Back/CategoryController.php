<?php

namespace App\Controller\Back;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    #[Route('/categorie', name: 'back_app_category')]
    public function index(): Response
    {
        return $this->render('back/category/index.html.twig', [
            'controller_name' => 'CategoryController',
        ]);
    }
}
