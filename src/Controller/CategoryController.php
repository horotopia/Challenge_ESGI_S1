<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use App\Form\CategoryType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;


use Symfony\Component\Security\Http\Attribute\IsGranted;
#[IsGranted('ROLE_ENTREPRISE')]

class CategoryController extends AbstractController
{
    #[Route('/admin/product-category-management', name: 'app_category')]
    public function addCategory(Request $request, EntityManagerInterface $entityManager, TokenGeneratorInterface $tokenGenerator , CategoryRepository $categoryRepository): Response
    {
        $category= new Category();
        $form=$this->createForm(CategoryType::class,$category);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            //generate token
            $tokenRegistration= $tokenGenerator->generateToken();
            
            $category->setName($form->get('name')->getData());
            $category->setDescription($form->get('description')->getData());
            $entityManager->persist($category);
            $entityManager->flush();

            return $this->redirectToRoute('app_category');

        }
        // $categories = $entityManager->getRepository(Category::class)->findAll();
        $categories = $entityManager->getRepository(Category::class)->getCategoriesWithProductCount($request->query->getInt('page', 1));
            // $categories=$catRepo->getCategoriesWithProductCount();
 
        return $this->render('back/product_category_management/index.html.twig', [
            'formCategory' => $form->createView(),
            'categories' => $categories,
        ]);
    }

    #[Route('/admin/product-category-management/delete/{id}', name: 'app_category_delete')]
    public function deleteCategory(Request $request, EntityManagerInterface $entityManager, TokenGeneratorInterface $tokenGenerator, Category $category)
    {
        $entityManager->remove($category);
        $entityManager->flush();
    
        return new JsonResponse(['success' => true]);
    }

}