<?php

namespace App\Controller;
use DateTime;
use DateTimeImmutable;
use App\Entity\Product;
use App\Form\ProductType;
use App\Form\EditProductType;
use Symfony\Component\Security\Core\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class ProductController extends AbstractController
{
    #[Route('/admin/products', name: 'app_back_products')]
    public function addProduct(Request $request, EntityManagerInterface $entityManager, TokenGeneratorInterface $tokenGenerator, Security $security): Response
    {
        $user = $security->getUser();
        $userId = $user->getId();
        $product = new Product();
        $formProduct=$this->createForm(ProductType::class,$product);
        $formProductUpdate=$this->createForm(EditProductType::class,$product);
        $formProduct->handleRequest($request);
        $formProductUpdate->handleRequest($request);

        if($formProduct->isSubmitted() && $formProduct->isValid()){
        $currentDateTime = new DateTime();
        $product->setName($formProduct->get('name')->getData());
        $product->setDescription($formProduct->get('description')->getData());
        $product->setBrand($formProduct->get('brand')->getData());
        $product->setUnitPrice($formProduct->get('unitPrice')->getData());
        $product->setVAT($formProduct->get('VAT')->getData());
        $product->setAvailableQuantity($formProduct->get('availableQuantity')->getData());
        $product->setCreatedAt($currentDateTime);
        $product->setUpdatedAt($currentDateTime);

        $product->setUserCreated($userId);
        $product->setUserUpdated($userId);

        $product->setCategoryId($formProduct->get('categoryId')->getData());
        
        $entityManager->persist($product);
        $entityManager->flush();

        return $this->redirectToRoute('app_back_products');

        }
        $products= $entityManager->getRepository(Product::class)->findAll();

        return $this->render('back/product_category_management/product_index.html.twig', [
            'formProduct' => $formProduct->createView(),
            'formProductUpdate' => $formProductUpdate->createView(),
             'products' => $products,
        ]);
    }

    #[Route('/admin/products/delete/{id}', name: 'app_back_products_delete')]
    public function deleteCategory(Request $request, EntityManagerInterface $entityManager, TokenGeneratorInterface $tokenGenerator, Product $product)
    {
        $entityManager->remove($product);
        $entityManager->flush();
    
        return new JsonResponse(['success' => true]);
    }


    #[Route('/admin/products/info/{id}', name: 'app_back_products_update')]
    public function getProductInfo(Request $request, EntityManagerInterface $entityManager, Product $product, $id)
    {
        $myProduct = $entityManager->getRepository(Product::class)->find($id);
        
        $data = [
            'id' => $myProduct->getId(),
            'name' => $myProduct->getName(),
            'brand' => $myProduct->getBrand(),
            'price' => $myProduct->getUnitPrice(),
            'VAT' => $myProduct->getVAT(),
            'quantity' => $myProduct->getAvailableQuantity(),
            'categories' => $myProduct->getCategoryId(),
            'description' => $myProduct->getDescription(),
            
        ];
    
        return new JsonResponse($data);
    }


}
