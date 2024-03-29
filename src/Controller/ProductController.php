<?php

namespace App\Controller;
use App\Entity\Product;
use App\Form\product\EditProductType;
use App\Form\product\ProductType;
use App\Form\User\SearchType;
use App\Model\SearchData;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

#[IsGranted('ROLE_ENTREPRISE')]

class ProductController extends AbstractController
{
    #[Route('/admin/products', name: 'app_back_products')]
    public function addProduct(Request $request, EntityManagerInterface $entityManager, TokenGeneratorInterface $tokenGenerator, Security $security): Response
    {
        $user = $security->getUser();
        $userId = $user->getId();
        $companyId = $user->getCompanyId();

        $userRoles = $user->getRoles();
        $product = new Product();
        $errorsFormAdd=null;
        $errorsFormUpdate=null;
        //create 3 forms Add,update,search in one dynamic page
        $formProduct=$this->createForm(ProductType::class,$product,['company_id' => $companyId]);
        $formProductUpdate=$this->createForm(EditProductType::class,$product,['company_id' => $companyId]);
        $searchData = new SearchData();
        $formSearchProduct = $this->createForm(SearchType::class, $searchData);

        $formSearchProduct->handleRequest($request);
        $formProduct->handleRequest($request);
        $formProductUpdate->handleRequest($request);
        //here if update product case
        //here if add case
        if($formProductUpdate->isSubmitted() && !$formProductUpdate->isValid()) {
            $errorsFormUpdate = $formProductUpdate->getErrors(true, false);
        }elseif($formProductUpdate->isSubmitted() && $formProductUpdate->isValid()) {
        $id=$formProductUpdate->get('Id')->getData();
        $product = $entityManager->getRepository(Product::class)->find($id);
        $companyProductId =$product->getCompanyId();
        $companyUserId = $user->getCompanyId();
        //verification if has right to modify or no  
        if(($companyProductId==$companyUserId) or (in_array('ROLE_ADMIN', $userRoles))){
        $currentDateTime = new DateTime();
        $product->setName($formProductUpdate->get('name')->getData());
        $product->setDescription($formProductUpdate->get('description')->getData());
        $product->setBrand($formProductUpdate->get('brand')->getData());
        $product->setUnitPrice($formProductUpdate->get('unitPrice')->getData());
        $product->setVAT($formProductUpdate->get('VAT')->getData());
        $product->setAvailableQuantity($formProductUpdate->get('availableQuantity')->getData());
        $product->setUpdatedAt($currentDateTime);
        $product->setUserUpdated($userId);
        $product->setCategoryId($formProductUpdate->get('categoryId')->getData());
        $entityManager->persist($product);
        $entityManager->flush();
        $this->addFlash('success', 'Produit modifié avec succès.');
        return $this->redirectToRoute('app_back_products');
            }else{
        $this->addFlash('error', 'Modification non autorisé.');
        return $this->redirectToRoute('app_back_products');
            }
        }


        //here if add case
        if($formProduct->isSubmitted() && !$formProduct->isValid()) {
            $errorsFormAdd = $formProduct->getErrors(true, false);

        }elseif($formProduct->isSubmitted() && $formProduct->isValid()){
        $currentDateTime = new DateTime();
        $product->setName($formProduct->get('name')->getData());
        $product->setDescription($formProduct->get('description')->getData());
        $product->setBrand($formProduct->get('brand')->getData());
        $product->setUnitPrice($formProduct->get('unitPrice')->getData());
        $product->setVAT($formProduct->get('VAT')->getData());
        $product->setAvailableQuantity($formProduct->get('availableQuantity')->getData());
        $product->setCreatedAt($currentDateTime);
        $product->setUpdatedAt($currentDateTime);
        $product->setCompanyId($companyId);
        $product->setUserCreated($userId);
        $product->setUserUpdated($userId);
        $product->setCategoryId($formProduct->get('categoryId')->getData());
        $entityManager->persist($product);
        $entityManager->flush();
        $this->addFlash('success', 'Produit creeé avec succès.');
        return $this->redirectToRoute('app_back_products');
        }

        //here if search by data case
        if($formSearchProduct->isSubmitted() && $formSearchProduct->isValid()){
            $searchData = $formSearchProduct->getData();
            $products = $entityManager->getRepository(Product::class)->findByProductNameOrCategoryName($searchData,$request->query->getInt('page', 1),$companyId,$userRoles);
        }else{
            //here if get all products
       // $products= $entityManager->getRepository(Product::class)->getAllProducts($request->query->getInt('page', 1));
            $products= $entityManager->getRepository(Product::class)->getProducts($request->query->getInt('page', 1),$companyId,$userRoles);
        }

        return $this->render('back/product_category_management/product_index.html.twig', [
            'formProduct' => $formProduct->createView(),
            'formProductUpdate' => $formProductUpdate->createView(),
            'products' => $products,
            'formSearchProducts' => $formSearchProduct,
            'errorsFormAdd' => $errorsFormAdd,
            'errorsFormUpdate' => $errorsFormUpdate,
        ]);
    }

    #[Route('/admin/products/delete/{id}', name: 'app_back_products_delete')]
    public function deleteCategory(Request $request, EntityManagerInterface $entityManager, TokenGeneratorInterface $tokenGenerator, Product $product, Security $security)
    {
        $user = $security->getUser();
        $userId = $user->getId();
        $companyUserId = $user->getCompanyId();
        $userRoles = $user->getRoles();
        $companyProductId =$product->getCompanyId();
        
        if($companyProductId== $companyUserId){
        $entityManager->remove($product);
        $entityManager->flush();
        return new JsonResponse(['success' => true]);
        }else{
        return new JsonResponse(['success' => false]);
        }
        
    }


    #[Route('/admin/getProductInfo/{id}', name: 'app_back_products_getInfo')]
    public function getProductInfo(Request $request, EntityManagerInterface $entityManager, Product $product, $id)
    {
        $myProduct = $entityManager->getRepository(Product::class)->find($id);
        $productCategorie = $myProduct->getCategoryId();
        $categoryID = !empty($productCategories) ? $productCategories[0] : null;

        $data = [
            'id' => $myProduct->getId(),
            'name' => $myProduct->getName(),
            'brand' => $myProduct->getBrand(),
            'price' => $myProduct->getUnitPrice(),
            'vat' => $myProduct->getVAT(),
            'quantity' => $myProduct->getAvailableQuantity(),
            'categories' => $myProduct->getCategoryId(),
            'description' => $myProduct->getDescription(),
            
        ];


        return new JsonResponse($data);
    }


}
