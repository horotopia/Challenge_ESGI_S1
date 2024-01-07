<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Entity\Produit;
use App\Form\CategorieType;
use App\Form\ProduitType;
use DataTables\DataTablesFactory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class CategorieController extends AbstractController
{
    #[Route('/admin/gestion-prod-cat', name: 'app_categorie')]
    public function addCategorie(Request $request,EntityManagerInterface $entityManager,TokenGeneratorInterface $tokenGenerator): Response
    {
        $categorie= new Categorie();
        $form=$this->createForm(CategorieType::class,$categorie);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            //generate token
            $tokenRegistration= $tokenGenerator->generateToken();
            
            $categorie->setNom($form->get('nom')->getData());
            $categorie->setDescription($form->get('description')->getData());
            $entityManager->persist($categorie);
            $entityManager->flush();

            return $this->redirectToRoute('app_categorie');

        }
        $categories = $entityManager->getRepository(Categorie::class)->findAll();
        

        return $this->render('back/gestion-prod-cat/index.html.twig', [
            'formCategorie' => $form->createView(),
            'categories' => $categories,
        ]);
    }

    #[Route('/admin/gestion-prod-cat/delete-cat/{id}', name: 'delete_categorie')]
    public function deleteCategorie(Request $request,EntityManagerInterface $entityManager,TokenGeneratorInterface $tokenGenerator,Categorie $category)
    {
        $entityManager->remove($category);
        $entityManager->flush();
    
        return new JsonResponse(['success' => true]);
    }

}
