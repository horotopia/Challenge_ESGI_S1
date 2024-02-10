<?php


namespace App\Controller;

use App\Entity\Invoice;
use App\Entity\Quote;
use App\Entity\QuoteProduct;
use App\Entity\Product;
use App\Form\Quote\AddType;
use App\Form\Quote\EditType;
use App\Form\User\SearchType;
use App\Model\SearchData;
use App\Repository\ClientRepository;
use App\Repository\QuoteProductRepository;
use App\Repository\QuoteRepository;
use App\Repository\CompanyRepository;
use App\Repository\ProductRepository;
use App\Service\PDFService;
use App\Service\SendEmail;
use Doctrine\ORM\EntityManagerInterface;
use MongoDB\Driver\Session;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ENTREPRISE')]
class QuoteController extends AbstractController
{
    #[Route('/admin/quotes', name: 'app_back_quotes')]
    public function index(QuoteRepository $quoteRepository, Request $request): Response
    {
        $searchData = new SearchData();
        $form = $this->createForm(SearchType::class, $searchData);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $searchData = $form->getData();
            $quoteList = $quoteRepository->findBySearchData($searchData);
        } else {

            $quoteList = $quoteRepository->findQuoteDetails($request->query->getInt('page', 1));
        }

        return $this->render('back/quotes/index.html.twig', [
            'controller_name' => 'Devis',
            'form' => $form->createView(),
            'quoteList' => $quoteList,
        ]);
    }
    #[Route('/admin/sendEmail/{quotationNumber}', name: 'app_back_sendEmail')]
    public function sendEmail(string $quotationNumber, MailerInterface $mailer,ClientRepository $clientRepository,CompanyRepository $companyRepository,PDFService $PDFService, EntityManagerInterface $entityManager,SessionInterface $session,QuoteProductRepository $quoteProductRepository): Response
    {

        $quote = $entityManager->getRepository(Quote::class)->findOneBy(['quotationNumber' => $quotationNumber]);
        $clientInfo = $clientRepository->find($quote->getClientId());
        $companyInfo = $companyRepository->find($this->getUser()->getCompanyId());
        $productList = $session->get('productList', []);

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

        $html = $this->render('back/quotes/detail.html.twig', [
            'quotationNumber' => $quote->getQuotationNumber(),
            'createdAt' => $quote->getCreatedAt()->format('Y-m-d'),
            'totalTHT' => $quote->getTotalHT(),
            'totalTTC' => $quote->getTotalTTC(),
            'dueDate' => $quote->getDueDate()->format('Y-m-d'),
            'client' => $clientInfo,
            'productList' => $products,
            'companyInfo' => $companyInfo
        ]);

        $pdfContent = $PDFService->generatePDF($html);
        $email = (new TemplatedEmail())
            ->from('ali.khelifa@se.univ-bejaia.dz')
            ->to($clientInfo->getEmail())
            ->subject('Votre devis')
            ->htmlTemplate('back/quotes/send_quote_email.html.twig')
            ->context([
                'quotationNumber' => $quotationNumber,
                'client' => $clientInfo,
            'acceptToken' => $acceptToken,
            'refuseToken' => $refuseToken
            ]);
        $email->attach($pdfContent, $quotationNumber, 'application/pdf');
        $mailer->send($email);
        $quote->setStatus('Envoyé');
        $entityManager->persist($quote);
        $entityManager->flush();

        $this->addFlash('success', 'Le devis a été envoyé par e-mail avec succès');


        return $this->redirectToRoute('app_back_quotes');
    }


    #[Route('/admin/quotes/add', name: 'app_back_quotes_add')]
    public function addQuote(Request $request, ProductRepository $productRepository, ClientRepository $clientRepository, CompanyRepository $companyRepository, SessionInterface $session, PDFService $PDFService, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AddType::class);
        $productList = $session->get('productList',[]);

        $totalTHT = $request->query->get('totalTHT', 0);
        $totalTTC = $request->query->get('totalTTC', 0);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($request->request->has('addProduct')) {
                $productId = $form->get('productId')->getData();
                $quantity = $form->get('availableQuantity')->getData();
                if ($productId) {
                    $product = $productRepository->find($productId);

                    if ($quantity >= 1) {
                        if ($product) {
                            $productList = $session->get('productList', []);
                            $productExists = false;
                            foreach ($productList as &$item) {

                                if ($item['id'] === $product->getId()) {
                                    $item['quantity'] += $quantity;
                                    $tht = str_replace(',', '', $product->getUnitPrice() * $item['quantity']);
                                    $tcc = str_replace(',', '', $product->getUnitPrice() * $item['quantity'] * (1 + $product->getVAT() / 100));
                                    $item['THT'] = number_format($tht, 2);
                                    $item['TTC'] = number_format($tcc, 2);

                                    $productExists = true;
                                    break;
                                }

                            }

                            if (!$productExists) {
                                $tht = str_replace(',', '', $product->getUnitPrice() * $quantity);
                                $tcc = str_replace(',', '', $product->getUnitPrice() * $quantity * (1 + $product->getVAT() / 100));

                                $productList[] = [
                                    'id' => $product->getId(),
                                    'name' => $product->getName(),
                                    'unitPrice' => number_format($product->getUnitPrice(), 2),
                                    'quantity' => $quantity,
                                    'THT' => number_format($tht, 2),
                                    'VAT' => $product->getVAT(),
                                    'TTC' => number_format($tcc, 2)
                                ];
                            }

                            $session->set('productList', $productList);
                            $this->addFlash("success", "Product ajouté avec succès!");
                        } else {
                            $this->addFlash("error", "Product non trouvé dans la base de données.");
                        }
                    } else {
                        $this->addFlash("error", "La quantité doit être supérieure à zéro.");
                    }
                } else {
                    $this->addFlash("error", "Vous n'avez pas sélectionné de products.");
                }
            }

            foreach ($productList as $product) {
                $totalTHT += floatval(str_replace(',', '', $product['THT']));
                $totalTTC += floatval(str_replace(',', '', $product['TTC']));
            }
            if ($request->request->has('createQuote')) {

                $quote = new Quote();
                $quotationNumber = 'DEV' . '-' . uniqid();
                $quote->setQuotationNumber($quotationNumber);
                $quote->setTotalHT($totalTHT);
                $quote->setTotalTTC($totalTTC);
                $quote->setClientId($form->get('clientId')->getData());
                $quote->setUserCreated($this->getUser()->getLastName());
                $quote->setStatus($form->get('status')->getData());
                $quote->setInstallments(1);
                $quote->setCreatedAt(new \DateTime());
                $quote->setDueDate($form->get('dueDate')->getData());
                foreach ($productList as $productData) {
                    $productId = $productData['id'];
                    $product = $entityManager->getRepository(Product::class)->find($productId);
                    if ($product) {
                        $quoteProduct = new QuoteProduct();
                        $quoteProduct->setProduct($product);
                        $quoteProduct->setQuantity($productData['quantity']);
                        $quote->addQuoteProduct($quoteProduct);

                    }
                }
                $entityManager->persist($quote);
                $entityManager->flush();
                $session->clear();
                $this->addFlash("success","Votre quotes a été créé avec succès");
                return $this->redirectToRoute('app_back_quotes');

            }
        }

        return $this->render('back/quotes/add.html.twig', [
            'form' => $form->createView(),
            'controller_name' => 'Ajouter un devis',
            'productList' => $productList,
            'totalTHT' => $totalTHT,
            'totalTTC' => $totalTTC
        ]);
    }

        #[Route('/admin/quotes/product/delete/{type}/{id}/{quotationNumber?}', name: 'app_back_quotes_remove_product')]
    public function removeProduct(Request $request, $id, string $type, ?string $quotationNumber, QuoteProductRepository $quoteProductRepository, QuoteRepository $quoteRepository, EntityManagerInterface $entityManager): Response
    {
        $session = $request->getSession();
        $productList = $session->get('productList', []);
        $quoteProduct = $quoteProductRepository->findOneBy(['product' => $id]);

        if ($type == "edit" && $quoteProduct != null) {
            $entityManager->remove($quoteProduct);
            $entityManager->flush();
            $quote = $quoteRepository->findOneBy(['quotationNumber' => $quoteProduct->getQuote()->getQuotationNumber()]);
            $quoteProducts = $quoteProductRepository->findBy(['quote' => $quote]);

            $productList = [];
            foreach ($quoteProducts as $quoteProduct) {
                $product = $quoteProduct->getProduct();
                $tht = str_replace(',', '', $product->getUnitPrice() * $quoteProduct->getQuantity());
                $ttc = str_replace(',', '', $product->getUnitPrice() * $quoteProduct->getQuantity() * (1 + $product->getVAT() / 100));
                $productList[] = [
                    'id' => $product->getId(),
                    'name' => $product->getName(),
                    'quotationNumber' => $quoteProduct->getQuote()->getQuotationNumber(),
                    'description' => $product->getDescription(),
                    'availableQuantity' => $quoteProduct->getQuantity(),
                    'unitPrice' => $product->getUnitPrice(),
                    'VAT' => $product->getVAT(),
                    'THT' => number_format($tht, 2),
                    'TTC' => number_format($ttc, 2)
                ];
            }
        }

        foreach ($productList as $key => $product) {
            if ($product['id'] == $id) {
                unset($productList[$key]);
                break;
            }
        }
        $this->addFlash("success", "Product supprimé avec succès!");

        $session->set('productList', $productList);
        $totalTHT = 0;
        $totalTTC = 0;

        foreach ($productList as $product) {
            $totalTHT += floatval($product['THT']);
            $totalTTC += floatval($product['TTC']);
        }

        if ($type == "add") {
            return $this->redirectToRoute('app_back_quotes_add', [
                'totalTHT' => $totalTHT,
                'totalTTC' => $totalTTC,
            ]);
        }


        if ($quotationNumber) {
            return $this->redirectToRoute('app_back_quotes_edit', [
                'totalTHT' => $totalTHT,
                'totalTTC' => $totalTTC,
                'quotationNumber' => $quotationNumber,
            ]);
        }
    }

    #[Route('/admin/quotes/info', name: 'app_back_quotes_product_info')]
    public function getProductInfo(Request $request, ProductRepository $repository): JsonResponse
    {
        $productId = $request->get('productId');

        $product = $repository->find($productId);

        $data = [];
        if ($product instanceof Product) {
            $data['unitPrice'] = $product->getUnitPrice();
            $data['VAT'] = $product->getVAT();
        }

        return new JsonResponse($data);
    }





    #[Route('/admin/quotes/generate/pdf/{quotationNumber}', name: 'app_back_quotes_generate_pdf')]
    public function generatePDF(EntityManagerInterface $entityManager, PDFService $PDFService, string $quotationNumber, CompanyRepository $companyRepository, ClientRepository $clientRepository, QuoteProductRepository $quoteProductRepository, SessionInterface $session): Response
    {
        $quote = $entityManager->getRepository(Quote::class)->findOneBy(['quotationNumber' => $quotationNumber]);
        $clientInfo = $clientRepository->find($quote->getClientId());
        $companyInfo = $companyRepository->find($this->getUser()->getCompanyId());
        $productList = $session->get('productList', []);

        $products = $quoteProductRepository->findBy(['quote' => $quote]);

        foreach ($products as $key => $product) {
            $unitPrice = $product->getProduct()->getUnitPrice();
            $quantity = $product->getQuantity();
            $VAT = $product->getProduct()->getVAT();


            $tht = str_replace(',', '',number_format($unitPrice * $quantity, 2));

            // Calculate TTC
            $ttc =str_replace(',', '', number_format(($tht + ($tht * $VAT / 100)), 2));

            // Add THT and TTC to the product array
            $products[$key]->THT = $tht;
            $products[$key]->TTC = $ttc;
            $products[$key]->unitPrice = $unitPrice;
        }

        $html = $this->render('back/quotes/detail.html.twig', [
            'quotationNumber' => $quote->getQuotationNumber(),
            'createdAt' => $quote->getCreatedAt()->format('Y-m-d'),
            'totalTHT' => $quote->getTotalHT(),
            'totalTTC' => $quote->getTotalTTC(),
            'dueDate' => $quote->getDueDate()->format('Y-m-d'),
            'client' => $clientInfo,
            'productList' => $products,
            'companyInfo' => $companyInfo
        ]);

        $PDFService->showPDF($html, $quote->getQuotationNumber());

        return new Response();

    }
    #[Route('/admin/quotes/delete/{quotationNumber}', name: 'app_back_quotes_delete')]
    public function deleteQuote(EntityManagerInterface $entityManager, string $quotationNumber): Response
    {
        $quote = $entityManager->getRepository(Quote::class)->findOneBy(['quotationNumber' => $quotationNumber]);

        if (!$quote) {
            $this->addFlash('error', 'Devis non trouvé.');
            return $this->redirectToRoute('app_back_quotes');
        }

        $entityManager->remove($quote);
        $entityManager->flush();

        $this->addFlash('success', 'Devis supprimé avec succès');

        return $this->redirectToRoute('app_back_quotes');
    }



    #[Route('/admin/quotes/edit/{quotationNumber}', name: 'app_back_quotes_edit')]
    public function editQuote(Request $request, string $quotationNumber, QuoteRepository $quoteRepository, ProductRepository $productRepository, EntityManagerInterface $entityManager, SessionInterface $session, QuoteProductRepository $quoteProductRepository): Response
    {      $productList = $session->get('productList', []);
        $quote = $quoteRepository->findOneBy(['quotationNumber' => $quotationNumber]);

        if(empty($productList)){
            $quoteProducts = $quoteProductRepository->findBy(['quote' => $quote]);
            foreach ($quoteProducts as $quoteProduct) {
                $product = $quoteProduct->getProduct();

                $tht = str_replace(',', '', $product->getUnitPrice() * $quoteProduct->getQuantity());
                $ttc = str_replace(',', '', $product->getUnitPrice() * $quoteProduct->getQuantity() * (1 + $product->getVAT() / 100));
                $productList[] = [
                    'id' => $product->getId(),
                    'name' => $product->getName(),
                    'quotationNumber' => $quotationNumber,
                    'description' => $product->getDescription(),
                    'quantity' => $quoteProduct->getQuantity(),
                    'unitPrice' => $product->getUnitPrice(),
                    'VAT' => $product->getVAT(),
                    'THT' => number_format($tht, 2),
                    'TTC' => number_format($ttc, 2)
                ];
            }
            $session->set('productList', $productList);
        }
        $form = $this->createForm(EditType::class);
        $form->handleRequest($request);

        $totalTHT = 0;
        $totalTTC = 0;

        if ($form->isSubmitted() && $form->isValid()) {

            if ($request->request->has('addProduct')) {
                $productId = $form->get('productId')->getData();
                $quantity = $form->get('availableQuantity')->getData();
                if ($productId) {
                    $product = $productRepository->find($productId);

                    if($quantity >= 1){
                        if ($product) {
                            $productList = $session->get('productList', []);

                            // Vérifier si le products existe déjà dans la liste
                            $productExists = false;
                            foreach ($productList as &$item) {
                                if ($item['id'] === $product->getId()) {

                                    $item['quantity'] += $quantity;

                                    $tht = str_replace(',', '', $product->getUnitPrice() * $item['quantity']);
                                    $tcc = str_replace(',', '', $product->getUnitPrice() * $item['quantity'] * (1 + $product->getVAT() / 100));
                                    $item['THT'] = number_format($tht, 2);
                                    $item['TTC'] = number_format($tcc, 2);

                                    $productExists = true;
                                    break;
                                }
                            }

                            if (!$productExists) {
                                // Ajouter le products à la liste s'il n'existe pas déjà
                                $tht = str_replace(',', '', $product->getUnitPrice() * $quantity);
                                $tcc = str_replace(',', '', $product->getUnitPrice() * $quantity * (1 + $product->getVAT() / 100));

                                $productList[] = [
                                    'id' => $product->getId(),
                                    'name' => $product->getName(),
                                    'unitPrice' => number_format($product->getUnitPrice(), 2),
                                    'quantity' => $quantity,
                                    'quotationNumber' => $quotationNumber,
                                    'THT' => number_format($tht, 2),
                                    'VAT' => $product->getVAT(),
                                    'TTC' => number_format($tcc, 2)
                                ];
                            }

                            $session->set('productList', $productList);
                            $this->addFlash("success", "Product ajouté avec succès!");
                        } else {
                            $this->addFlash("error", "Product non trouvé dans la base de données.");
                        }
                    } else {
                        $this->addFlash("error", "La quantité doit être supérieure à zéro.");
                    }
                } else {
                    $this->addFlash("error", "Vous n'avez pas sélectionné de products.");
                }
            }


            foreach ($productList as $product) {
                $totalTHT += floatval(str_replace(',', '', $product['THT']));
                $totalTTC += floatval(str_replace(',', '', $product['TTC']));
            }

            if ($request->request->has('createQuote')) {
                $quote->setTotalHT($totalTHT);
                $quote->setTotalTTC($totalTTC);
                $quote->setClientId($form->get('clientId')->getData());
                $quote->setUserCreated($this->getUser()->getLastName());
                $quote->setStatus($form->get('status')->getData());
                $quote->setInstallments(1);
                $quote->setCreatedAt(new \DateTime());
                $quote->setDueDate($form->get('dueDate')->getData());
                $entityManager->persist($quote);
                $entityManager->flush();
                foreach ($productList as $productData) {
                    $productId = $productData['id'];
                    $quantity = $productData['quantity'];
                    $existingQuoteProduct = null;
                    foreach ($quote->getQuoteProducts() as $quoteProduct) {
                        if ($quoteProduct->getProduct()->getId() === $productId) {
                            $existingQuoteProduct = $quoteProduct;
                            break;
                        }
                    }

                    if ($existingQuoteProduct) {
                        if ($existingQuoteProduct->getQuantity() !== $quantity) {
                            $existingQuoteProduct->setQuantity($quantity);
                            $entityManager->persist($existingQuoteProduct);
                            $entityManager->flush();

                        }
                    } else {
                        $product = $entityManager->getRepository(Product::class)->find($productId);
                        if ($product) {
                            $quoteProduct = new QuoteProduct();
                            $quoteProduct->setProduct($product);
                            $quoteProduct->setQuantity($quantity);
                            $quote->addQuoteProduct($quoteProduct);
                            $entityManager->persist($quote);
                            $entityManager->flush();
                        }
                    }
                }

                $session->clear();
                $this->addFlash("success", "Votre quotes a été modifié avec succès");
                return $this->redirectToRoute('app_back_quotes');
            }
        }

        return $this->render('back/quotes/edit.html.twig', [
            'form' => $form->createView(),
            'controller_name' => 'Modifier un devis',
            'productList' => $productList,
            'totalTHT' => $totalTHT,
            'totalTTC' => $totalTTC
        ]);
    }

    #[Route('/admin/quotes/accept/{quotationNumber}/{token}', name: 'app_back_quotes_accept')]
    public function acceptQuote(string $quotationNumber, string $token, EntityManagerInterface $entityManager, PDFService $pdfService): Response
    {
        $quote = $entityManager->getRepository(Quote::class)->findOneBy(['quotationNumber' => $quotationNumber]);

        if (!$quote) {
            $this->addFlash('error', 'Devis non trouvé.');
            return $this->redirectToRoute('app_back_quotes');
        }

        if ($quote->getAcceptToken() !== $token) {
            $this->addFlash('error', 'Devis déjà traité.');
            return $this->redirectToRoute('app_back_quotes');
        }

        if (!in_array($quote->getStatus(), ['Brouillon', 'Envoyé'])) {
            $this->addFlash('error', 'Le devis ne peut pas être accepté dans son état actuel.');
            return $this->redirectToRoute('app_back_quotes');
        }

        // Accepter le devis
        $quote->setStatus('Accepté');
        $quote->setAcceptToken(null);
        $quote->setRefuseToken(null);
        $entityManager->flush();

        // Créer une nouvelle facture
        $invoice = new Invoice();
        $invoice->setAmount($quote->getTotalTTC());
        // Générer un numéro de facture unique
        $invoiceNumber = 'INV' . '-' . uniqid();
        $invoice->setInvoiceNumber($invoiceNumber);
        $invoice->setStatus('En attente de paiement');
        $invoice->setCreatedAt(new \DateTimeImmutable());
        $invoice->setDueDate(new \DateTime('+30 days'));
        $invoice->setClientId($quote->getClientId());
        $invoice->setQuoteId($quote);

        $entityManager->persist($invoice);
        $entityManager->flush();

        $html = $this->renderView('back/invoices/invoice_template.html.twig', [
            'invoice' => $invoice,
            'quote' => $quote,
        ]);

        $pdfContent = $pdfService->generatePDF($html);

        return new Response($pdfContent, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $invoiceNumber . '.pdf"',
        ]);
    }

    #[Route('/admin/quotes/refuse/{quotationNumber}/{token}', name: 'app_back_quotes_refuse')]
    public function refuseQuote(string $quotationNumber, string $token, EntityManagerInterface $entityManager): Response
    {
        $quote = $entityManager->getRepository(Quote::class)->findOneBy(['quotationNumber' => $quotationNumber]);

        if (!$quote) {
            $this->addFlash('error', 'Devis non trouvé.');
            return $this->redirectToRoute('app_back_quotes');
        }

        if ($quote->getRefuseToken() !== $token) {
            $this->addFlash('error', 'Devis déjà traité.');
            return $this->redirectToRoute('app_back_quotes');
        }

        if ($quote->getStatus() !== 'Envoyé') {
            $this->addFlash('error', 'Le devis ne peut être refusé que s\'il est en statut "Envoyé".');
            return $this->redirectToRoute('app_back_quotes');
        }

        $quote->setStatus('Refusé');
        $quote->setAcceptToken(null);
        $quote->setRefuseToken(null);
        $entityManager->flush();
        $this->addFlash('success', 'Le devis a été refusé.');

        return $this->redirectToRoute('app_back_quotes');
    }

    #[Route('/invoice/download/{id}', name: 'invoice_download')]
    public function downloadInvoice(int $id, EntityManagerInterface $entityManager, CompanyRepository $companyRepository, PDFService $pdfService): Response
    {
        $companyInfo = $companyRepository->find($this->getUser()->getCompanyId());
        $invoice = $entityManager->getRepository(Invoice::class)->find($id);
        if (!$invoice) {
            throw $this->createNotFoundException('La facture demandée n\'existe pas.');
        }

        $quote = $invoice->getQuoteId();
        $products = $quote->getQuoteProducts();

        $productList = [];
        $totalTHT = 0;
        $totalTTC = 0;
        foreach ($products as $product) {
            $productDetail = [
                'product' => $product->getProduct()->getName(),
                'quantity' => $product->getQuantity(),
                'unitPrice' => $product->getProduct()->getUnitPrice(),
                'VAT' => $product->getProduct()->getVAT(),
//                'THT' => $product->getTHT(),
//                'TTC' => $product->getTTC(),
            ];
            $productList[] = $productDetail;

//            $totalTHT += $product->getTHT();
//            $totalTTC += $product->getTTC();
        }

        $html = $this->renderView('back/invoices/invoice_template.html.twig', [
            'invoice' => $invoice,
            'productList' => $productList,
            'totalTHT' => $totalTHT,
            'totalTTC' => $totalTTC,
            'companyInfo' => $companyInfo
        ]);

        $pdfContent = $pdfService->generatePDF($html);

        return new Response($pdfContent, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="invoice_' . $invoice->getId() . '.pdf"',
        ]);
    }

    #[Route('/payment/full/{id}', name: 'payment_full')]
    public function payFull(int $id, EntityManagerInterface $entityManager) {
        $invoice = $entityManager->getRepository(Invoice::class)->find($id);
        if (!$invoice) {
            throw $this->createNotFoundException('La facture demandée n\'existe pas.');
        }

        // Logique de paiement intégral à mettre en place
        // Rediriger vers une passerelle de paiement avec le montant total

        return $this->render('back/payments/full_payment.html.twig', [
            'invoice' => $invoice
        ]);
    }

    #[Route('/payment/installments/{id}', name: 'payment_installments')]
    public function payInInstallments(int $id, EntityManagerInterface $entityManager) {
        $invoice = $entityManager->getRepository(Invoice::class)->find($id);
        if (!$invoice) {
            throw $this->createNotFoundException('La facture demandée n\'existe pas.');
        }

        // Logique pour le paiement en plusieurs fois à mettre en place
        // Créer un plan de paiement et de rediriger l'utilisateur vers une interface pour choisir le plan

        return $this->render('back/payments/installments_payment.html.twig', [
            'invoice' => $invoice
        ]);
    }

    #[Route('/payment/deposit/{id}', name: 'payment_deposit')]
    public function payDeposit(int $id, EntityManagerInterface $entityManager) {
        $invoice = $entityManager->getRepository(Invoice::class)->find($id);
        if (!$invoice) {
            throw $this->createNotFoundException('La facture demandée n\'existe pas.');
        }

        // Logique pour le paiement d'un acompte à mettre en place
        // Définir un montant fixe ou un pourcentage de l'acompte et de rediriger l'utilisateur vers une passerelle de paiement

        return $this->render('back/payments/deposit_payment.html.twig', [
            'invoice' => $invoice
        ]);
    }
}