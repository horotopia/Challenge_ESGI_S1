<?php

namespace App\Controller;

use App\Entity\Facture;
use App\Form\facture\FactureType;
use App\Repository\DevisRepository;
use App\Repository\FactureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[IsGranted('ROLE_ENTERPRISE')]
class FactureController extends AbstractController
{
    #[Route('/admin/factures', name: 'app_back_facture')]
    public function index(FactureRepository $factureRepository): Response
    {
        $factures = $factureRepository->findAll();

        return $this->render('back/facture/index.html.twig', [
            'factures' => $factures,
        ]);
    }

    #[Route('/admin/facture/new', name: 'app_back_new_facture')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(FactureType::class);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $devis = $form->get('devis')->getData();
    
            $facture = new Facture();
            $facture->setMontant($devis->getTotalTTC());
    
            $entityManager->persist($facture);
            $entityManager->flush();
    
            $this->addFlash('success', 'La facture a été créée avec succès.');
    
            return $this->redirectToRoute('app_back_facture');
        }
    
        return $this->render('back/facture/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }    
}
