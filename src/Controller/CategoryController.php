<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Product;
use App\Form\category\CategoryType;
use App\Form\category\EditCategoryType;
use App\Form\User\SearchType;
use App\Model\SearchData;
use App\Repository\CategoryRepository;
use DataTables\DataTablesFactory;
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


#[IsGranted('ROLE_ENTREPRISE')]
class CategoryController extends AbstractController
{
    #[Route('/admin/product-category-management', name: 'product_category_management')]
    public function addCategory(Request $request, EntityManagerInterface $entityManager, TokenGeneratorInterface $tokenGenerator , CategoryRepository $categoryRepository, Security $security): Response
    {
        $user = $security->getUser();
        $userId = $user->getId();
        $companyId = $user->getCompanyId();
        $userRoles = $user->getRoles();

        $category= new Category();
        $errorsFormAdd=null;
        $errorsFormUpdate=null;
        $searchData = new SearchData();

        $form=$this->createForm(CategoryType::class,$category);
        $formCategoryUpdate=$this->createForm(EditCategoryType::class,$category);
        $formSearchCategory = $this->createForm(SearchType::class, $searchData);

        $formSearchCategory->handleRequest($request);
        $form->handleRequest($request);
        $formCategoryUpdate->handleRequest($request);

        if($form->isSubmitted() && !$form->isValid()) {
            $errorsFormAdd = $form->getErrors(true, false);
        }elseif($form->isSubmitted() && $form->isValid()) {
            //generate token
            $currentDateTime = new DateTime();
            $tokenRegistration= $tokenGenerator->generateToken();
            $category->setName($form->get('name')->getData());
            $category->setDescription($form->get('description')->getData());
            $category->setCreatedAt($currentDateTime);
            $category->setUpdatedAt($currentDateTime);
            $category->setCompanyId($companyId);
            $category->setUserCreated($user);
            $category->setUserUpdated($user);
            $entityManager->persist($category);
            $entityManager->flush();
            return $this->redirectToRoute('product_category_management');
        }

        if($formCategoryUpdate->isSubmitted() && !$formCategoryUpdate->isValid()) {
            $errorsFormUpdate = $form->getErrors(true, false);
        }elseif($formCategoryUpdate->isSubmitted() && $formCategoryUpdate->isValid()) {
            $currentDateTime = new DateTime();
            $id=$formCategoryUpdate->get('id')->getData();
            $category = $entityManager->getRepository(Category::class)->find($id);
            $category->setName($formCategoryUpdate->get('name')->getData());
            $category->setDescription($formCategoryUpdate->get('description')->getData());
            $category->setUpdatedAt($currentDateTime);
            $category->setUserUpdated($user);
            $entityManager->persist($category);
            $entityManager->flush();
            return $this->redirectToRoute('product_category_management');
        }

        //here if search by data case
        if($formSearchCategory->isSubmitted() && $formSearchCategory->isValid()){
            $searchData = $formSearchCategory->getData();
            $categories = $entityManager->getRepository(Category::class)->getCategoriesBySearch($searchData,$request->query->getInt('page', 1),$companyId,$userRoles);
        }else{
        // $categories = $entityManager->getRepository(Category::class)->findAll();
        $categories = $entityManager->getRepository(Category::class)->getCategoriesWithProductCount($request->query->getInt('page', 1),$companyId,$userRoles);
            // $categories=$catRepo->getCategoriesWithProductCount();
        }
        return $this->render('back/product_category_management/index.html.twig', [
            'formCategory' => $form->createView(),
            'categories' => $categories,
            'formSearchCategory' => $formSearchCategory,
            'formCategoryUpdate' => $formCategoryUpdate,
            'errorsFormAdd' => $errorsFormAdd,
            'errorsFormUpdate' => $errorsFormUpdate,
        ]);
    }

    #[Route('/admin/product-category-management/delete/{id}', name: 'app_category_delete')]
    public function deleteCategory(Request $request, EntityManagerInterface $entityManager, TokenGeneratorInterface $tokenGenerator, Category $category, Security $security)
    {
        $user = $security->getUser();
        $userId = $user->getId();
        $companyUserId = $user->getCompanyId();
        $userRoles = $user->getRoles();
        $companyCategorieId =$category->getCompanyId();
        if($companyCategorieId== $companyUserId){
        $entityManager->remove($category);
        $entityManager->flush();
        return new JsonResponse(['success' => true]);
        }else{
            $this->addFlash('error', "Opération refusé");
            return $this->redirectToRoute('product_category_management');
        }
        
    }

}
