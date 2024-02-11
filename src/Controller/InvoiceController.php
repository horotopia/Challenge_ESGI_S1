<?php

namespace App\Controller;

use App\Entity\Invoice;
use App\Form\Invoice\InvoiceType;
use App\Form\User\SearchType;
use App\Model\SearchData;
use App\Repository\QuoteRepository;
use App\Repository\InvoiceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[IsGranted('ROLE_ENTREPRISE')]
class InvoiceController extends AbstractController
{
    #[Route('/admin/invoices', name: 'app_back_invoices')]
    public function index(InvoiceRepository $invoiceRepository, Request $request): Response
    {
        $searchData = new SearchData();
        $form = $this->createForm(SearchType::class, $searchData);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $searchData = $form->getData();
            $invoiceList = $invoiceRepository->findBySearchData($searchData);
        } else {

            $invoiceList = $invoiceRepository->findInvoiceDetails($request->query->getInt('page', 1));
        }

        return $this->render('back/invoices/index.html.twig', [
            'controller_name' => 'Invoices',
            'form' => $form->createView(),
            'invoiceList' => $invoiceList,
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
