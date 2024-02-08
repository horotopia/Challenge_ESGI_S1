<?php


namespace App\Controller;

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
            ->html('<p>Bonjour,</p><p>Veuillez trouver en pièce jointe votre devis.</p>');
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



}

