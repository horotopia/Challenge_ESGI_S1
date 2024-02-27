<?php

namespace App\Controller;

use App\Entity\Invoice;
use App\Entity\Quote;
use App\Form\Invoice\InvoiceType;
use App\Form\PaymentType\PaymentType;
use App\Form\PaymentType\UpdatePaymentType;
use App\Form\User\SearchType;
use App\Model\SearchData;
use App\Repository\ClientRepository;
use App\Repository\CompanyRepository;
use App\Repository\QuoteProductRepository;
use App\Repository\QuoteRepository;
use App\Repository\InvoiceRepository;
use App\Service\PDFService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[IsGranted('ROLE_ENTREPRISE')]
class InvoiceController extends AbstractController
{
    #[Route('/admin/invoices', name: 'app_back_invoices')]
    public function index(InvoiceRepository $invoiceRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $searchData = new SearchData();
        $companyId= $this->getUser()->getCompanyId()->getId();
        $userRole=$this->getUser()->getRoles();
        $formInvoice = $this->createForm(UpdatePaymentType::class ,null, ["companyId" => $companyId]);
        $form = $this->createForm(SearchType::class, $searchData);

        $form->handleRequest($request);
        $formInvoice->handleRequest($request);

        if ($formInvoice->isSubmitted() && $formInvoice->isValid()) {
            // $currentDateTime = new DateTimeImmutable();
            $formData = $formInvoice->getData();
            $curretDate = new \DateTime();
            $invoice = $entityManager->getRepository(Invoice::class)->find($formData['IdFacture']);
            $invoice->setPaymentDate($formData['dueDate']);
            $invoice->setStatus('Payé');
            $invoice->setPaymentMethod($formData['paymentMethod']);
            $invoice->setUpdatedAt($formData['dueDate']);
            $invoice->setTotalTTC($formData['totalTTC']);
            $entityManager->persist($invoice);
            $entityManager->flush();
            $this->addFlash('success', 'La facture a été payé avec succès.');
            return $this->redirectToRoute('app_back_invoices');
        }


        if ($form->isSubmitted() && $form->isValid()) {
            $searchData = $form->getData();
            $invoiceList = $invoiceRepository->findBySearchData($searchData,$userRole,$companyId);
        } else {

            $invoiceList = $invoiceRepository->findInvoiceDetails($request->query->getInt('page', 1),$userRole,$companyId);
        }

        return $this->render('back/invoices/index.html.twig', [
            'controller_name' => 'Invoices',
            'form' => $form->createView(),
            'formInvoice' => $formInvoice->createView(),
            'invoiceList' => $invoiceList,

        ]);
    }
    #[Route('/admin/invoices/add', name: 'app_back_invoices_add')]
    public function add(Request $request, EntityManagerInterface $entityManager, InvoiceRepository $invoiceRepository): Response
    {
        $companyId = $this->getUser()->getCompanyId()->getId();
        $form = $this->createForm(InvoiceType::class, null, ["companyId" => $companyId]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
            $existingInvoice = $invoiceRepository->findOneBy(['quote' => $formData['quotes']]);
            if ($existingInvoice) {
                $this->addFlash('error', 'Une facture existe déjà pour ce devis.');
                return $this->redirectToRoute('app_back_invoices_add');
            }

            $invoiceNumber= "FAC". '-' . uniqid();
            $invoice = new Invoice();
            $client = $formData['quotes']->getClientId();
            $totalHT=$formData['quotes']->gettotalHT();
            $totalTTC=$formData['quotes']->gettotalTTC();

            $invoice->setQuote($formData['quotes']);
            $invoice->setDueDate($formData['dueDate']);
            $invoice->setStatus($formData['statut']);
            $invoice->setInvoiceNumber($invoiceNumber);
            $invoice->setPaymentMethod($formData['paymentMethod']);
            $invoice->setTotalHT($totalHT);
            $invoice->setTotalTTC($totalTTC);
            $invoice->setClient($client);

            $entityManager->persist($invoice);
            $entityManager->flush();

            $this->addFlash('success', 'La facture a été créée avec succès.');

            return $this->redirectToRoute('app_back_invoices');
        }

        return $this->render('back/invoices/add.html.twig', [
            'form' => $form->createView(),
            'controller_name' => 'Ajouter une facture',
        ]);
    }


    #[Route('/admin/invoices/get-quote-details', name: 'app_back_invoices_get_quote_details')]
    public function getQuoteDetails(Request $request, QuoteRepository $quoteRepository): JsonResponse
    {
        $quoteId = $request->get('quote_id');

        $quote = $quoteRepository->find($quoteId);
        return new JsonResponse([
            'totalTTC' => $quote->getTotalTTC(),
            'client' => [
                'firstName' => $quote->getClientId()->getFirstName(),
                'lastName' => $quote->getClientId()->getLastName(),
            ]
        ]);
    }



    #[Route('/admin/invoices/download/{invoiceNumber}', name: 'invoice_download')]
    public function downloadInvoice(string $invoiceNumber, EntityManagerInterface $entityManager, CompanyRepository $companyRepository, PDFService $pdfService,QuoteProductRepository $quoteProductRepository,ClientRepository $clientRepository): Response
    {
        $companyInfo = $companyRepository->find($this->getUser()->getCompanyId());
        $invoice = $entityManager->getRepository(Invoice::class)->findOneBy(["invoiceNumber" => $invoiceNumber]);

        if (!$invoice) {
            throw $this->createNotFoundException('La facture demandée n\'existe pas.');
        }

        $quote = $invoice->getQuote();
        $clientInfo = $clientRepository->find($quote->getClientId());
        $products = $quoteProductRepository->findBy(['quote' => $quote]);


        foreach ($products as $key => $product) {
            $unitPrice = $product->getProduct()->getUnitPrice();
            $quantity = $product->getQuantity();
            $VAT = $product->getProduct()->getVAT();


            $tht = str_replace(',', '', number_format($unitPrice * $quantity, 2));

            // Calculate TTC
            $ttc = str_replace(',', '', number_format(($tht + ($tht * $VAT / 100)), 2));

            // Add THT and TTC to the product array
            $products[$key]->THT = $tht;
            $products[$key]->TTC = $ttc;
            $products[$key]->unitPrice = $unitPrice;
        }

        $html = $this->render('back/invoices/invoice_template.html.twig', [
            'invoiceInfo'=>$invoice,
            'client' => $clientInfo,
            'productList' => $products,
            'companyInfo' => $companyInfo
        ]);

        $pdfService->showPDF($html, $invoice->getInvoiceNumber());

        return new Response();
    }
    #[Route('/admin/sendEmailInvoice/{invoiceNumber}', name: 'app_back_sendEmail_invoice')]
    public function sendEmail(string $invoiceNumber, MailerInterface $mailer,ClientRepository $clientRepository,CompanyRepository $companyRepository,PDFService $PDFService, EntityManagerInterface $entityManager,SessionInterface $session,QuoteProductRepository $quoteProductRepository): Response
    {

        $companyInfo = $companyRepository->find($this->getUser()->getCompanyId());
        $invoice = $entityManager->getRepository(Invoice::class)->findOneBy(["invoiceNumber" => $invoiceNumber]);

        if (!$invoice) {
            throw $this->createNotFoundException('La facture demandée n\'existe pas.');
        }

        $quote = $invoice->getQuote();
        $clientInfo = $clientRepository->find($quote->getClientId());
        $products = $quoteProductRepository->findBy(['quote' => $quote]);

        $acceptToken = bin2hex(random_bytes(32));
        $refuseToken = bin2hex(random_bytes(32));

        $quote->setAcceptToken($acceptToken);
        $quote->setRefuseToken($refuseToken);

        foreach ($products as $key => $product) {
            $unitPrice = $product->getProduct()->getUnitPrice();
            $quantity = $product->getQuantity();
            $VAT = $product->getProduct()->getVAT();
            $tht = str_replace(',', '',number_format($unitPrice * $quantity, 2));
            $ttc =str_replace(',', '', number_format(($tht + ($tht * $VAT / 100)), 2));

            $products[$key]->THT = $tht;
            $products[$key]->TTC = $ttc;
            $products[$key]->unitPrice = $unitPrice;
        }

        $html = $this->render('back/invoices/invoice_template.html.twig', [
            'invoiceInfo'=>$invoice,
            'client' => $clientInfo,
            'productList' => $products,
            'companyInfo' => $companyInfo
        ]);
        $pdfContent = $PDFService->generatePDF($html);
        $email = (new TemplatedEmail())
            ->from('ali.khelifa@se.univ-bejaia.dz')
            ->to($clientInfo->getEmail())
            ->subject('Votre devis')
            ->htmlTemplate('back/invoices/send_invoice_email.html.twig')
            ->context([
                'quotationNumber' => $invoiceNumber,
                'client' => $clientInfo,
                'acceptToken' => $acceptToken,
                'refuseToken' => $refuseToken
            ]);
        $email->attach($pdfContent, $invoiceNumber, 'application/pdf');
        $mailer->send($email);
        $invoice->setStatus('Envoyé');
        $entityManager->persist($invoice);
        $entityManager->flush();

        $this->addFlash('success', 'Votre facture a été envoyé par e-mail avec succès');


        return $this->redirectToRoute('app_back_invoices');
    }
    #[Route('/admin/invoices/delete/{invoiceNumber}', name: 'invoice_delete')]
    public function deleteInvoice(string $invoiceNumber, EntityManagerInterface $entityManager, InvoiceRepository $invoiceRepository): RedirectResponse
    {
        $invoice = $invoiceRepository->findOneBy(['invoiceNumber' => $invoiceNumber]);

        if (!$invoice) {
            $this->addFlash('error', 'La facture que vous essayez de supprimer n\'existe pas.');
            return $this->redirectToRoute('app_back_invoices');
        }

        // Supprimer la facture de la base de données
        $entityManager->remove($invoice);
        $entityManager->flush();

        $this->addFlash('success', 'La facture a été supprimée avec succès.');
        return $this->redirectToRoute('app_back_invoices');
    }



}
