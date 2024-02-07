<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
#[IsGranted('ROLE_ENTERPRISE')]
class ProductCategoryManagementController extends AbstractController
{
    #[Route('/admin/product-category-management', name: 'app_back_product_category_management')]
    public function index(): Response
    {
        return $this->render('back/product_category_management/index.html.twig', [
            'controller_name' => 'ProductCategoryManagementController',
        ]);
    }
}
