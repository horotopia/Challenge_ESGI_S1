<?php

namespace App\Controller;

use App\Entity\Invoice;
use App\Form\Invoice\InvoiceType;
use App\Repository\QuoteRepository;
use App\Repository\InvoiceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[IsGranted('ROLE_ENTERPRISE')]
class InvoiceController extends AbstractController
{
    #[Route('/admin/invoices', name: 'app_back_invoices')]
    public function index(InvoiceRepository $invoiceRepository): Response
    {
        $invoices = $invoiceRepository->findAll();

        return $this->render('back/invoices/index.html.twig', [
            'invoices' => $invoices,
        ]);
    }

    #[Route('/admin/invoices/add', name: 'app_back_invoices_add')]
    public function add(Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(InvoiceType::class);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $quote = $form->get('quotes')->getData();
    
            $invoice = new Invoice();
            $invoice->setAmount($quote->getTotalTTC());
    
            $entityManager->persist($invoice);
            $entityManager->flush();
    
            $this->addFlash('success', 'Les factures a été créée avec succès.');
    
            return $this->redirectToRoute('app_back_invoices');
        }
    
        return $this->render('back/invoices/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }    
}
