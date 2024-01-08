<?php

namespace App\Controller;
use DateTimeImmutable;
use App\Entity\Produit;
use App\Form\ProduitType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class ProduitController extends AbstractController
{
    #[Route('/admin/produit', name: 'app_produit')]
    public function addProduit(Request $request,EntityManagerInterface $entityManager,TokenGeneratorInterface $tokenGenerator): Response
    {
        $produit = new Produit();
        $formProduit=$this->createForm(ProduitType::class,$produit);
        $formProduit->handleRequest($request);

        if($formProduit->isSubmitted() && $formProduit->isValid()){
        $currentDateTime = new DateTimeImmutable();
        $produit->setNom($formProduit->get('nom')->getData());
        $produit->setDescription($formProduit->get('description')->getData());
        $produit->setMarque($formProduit->get('marque')->getData());
        $produit->setPrixUnitaire($formProduit->get('prix_unitaire')->getData());
        $produit->setTva($formProduit->get('tva')->getData());
        $produit->setQuantiteDisponible($formProduit->get('quantite_disponible')->getData());
        $produit->setCreateAt($currentDateTime);
        $produit->setUpdateAt($currentDateTime);
        $produit->setIdCategorie($formProduit->get('id_categorie')->getData());
        
        $entityManager->persist($produit);
        $entityManager->flush();

        return $this->redirectToRoute('app_produit');

        }
        $produits= $entityManager->getRepository(Produit::class)->findAll();
        

        return $this->render('back/gestion-prod-cat/indexProduit.html.twig', [
            'formProduit' => $formProduit->createView(),
             'produits' => $produits,
        ]);
    }
}
