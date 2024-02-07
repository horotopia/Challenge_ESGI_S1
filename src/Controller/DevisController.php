<?php


namespace App\Controller;

use App\Entity\Devis;
use App\Entity\DevisProduit;
use App\Entity\Produit;
use App\Form\devis\AddTypeForm;
use App\Form\devis\EditTypeForm;
use App\Form\utilisateurs\SearchTypeForm;
use App\model\SearchData;
use App\Repository\ClientRepository;
use App\Repository\DevisProduitRepository;
use App\Repository\DevisRepository;
use App\Repository\EntrepriseRepository;
use App\Repository\ProduitRepository;
use App\Service\PDFService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ENTERPRISE')]
class DevisController extends AbstractController
{
    #[Route('/admin/devis', name: 'app_back_devis')]
    public function index(DevisRepository $devisRepository,Request $request): Response
    {
        $searchData = new SearchData();
        $form = $this->createForm(SearchTypeForm::class, $searchData);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $searchData = $form->getData();
            $devisList = $devisRepository->findBySearchData($searchData);
        } else {

            $devisList = $devisRepository->findDevisDetails($request->query->getInt('page', 1));
        }

        return $this->render('back/devis/index.html.twig', [
            'controller_name' => 'Devis',
            'form' => $form->createView(),
            'devisList' => $devisList,
        ]);
    }

    #[Route('/admin/devis/add', name: 'add_devis')]
    public function AddDevis(Request $request, ProduitRepository $produitRepository,ClientRepository $clientRepository,EntrepriseRepository $entrepriseRepository, SessionInterface $session,PDFService $PDFService,EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AddTypeForm::class);
        $productList = $session->get('productList', []);

        $totalTHT = $request->query->get('totalTHT', 0);
        $totalTTC = $request->query->get('totalTTC', 0);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($request->request->has('add_produit')) {
                $idProduit = $form->get('id_produit')->getData();
                $quantity = $form->get('quantite_disponible')->getData();
                if ($idProduit) {
                    $product = $produitRepository->find($idProduit);

                    if ($quantity >= 1) {
                        if ($product) {
                            $productList = $session->get('productList', []);
                            $productExists = false;
                            foreach ($productList as &$item) {
                                if ($item['id'] === $product->getId()) {
                                    $item['quantite'] += $quantity;

                                    $tht = str_replace(',', '', $product->getPrixUnitaire() * $item['quantite']);
                                    $tcc = str_replace(',', '', $product->getPrixUnitaire() * $item['quantite'] * (1 + $product->getTva() / 100));
                                    $item['THT'] = number_format($tht, 2);
                                    $item['TTC'] = number_format($tcc, 2);

                                    $productExists = true;
                                    break;
                                }
                            }

                            if (!$productExists) {
                                $tht = str_replace(',', '', $product->getPrixUnitaire() * $quantity);
                                $tcc = str_replace(',', '', $product->getPrixUnitaire() * $quantity * (1 + $product->getTva() / 100));

                                $productList[] = [
                                    'id' => $product->getId(),
                                    'nom' => $product->getNom(),
                                    'prix_unitaire' => number_format($product->getPrixUnitaire(), 2),
                                    'quantite' => $quantity,
                                    'THT' => number_format($tht, 2),
                                    'TVA' => $product->getTva(),
                                    'TTC' => number_format($tcc, 2)
                                ];
                            }

                            $session->set('productList', $productList);
                            $this->addFlash("success", "Produit ajouté avec succès!");
                        } else {
                            $this->addFlash("error", "Produit non trouvé dans la base de données.");
                        }
                    } else {
                        $this->addFlash("error", "La quantité doit être supérieure à zéro.");
                    }
                } else {
                    $this->addFlash("error", "Vous n'avez pas sélectionné de produit.");
                }
            }

            foreach ($productList as $product) {
                $totalTHT += floatval(str_replace(',', '', $product['THT']));
                $totalTTC += floatval(str_replace(',', '', $product['TTC']));
            }
            if ($request->request->has('create_devis')) {

                $devis = new Devis();
                $numDevis = 'DEV' . '-' . uniqid();
                $devis->setNumDevis($numDevis);
                $devis->setTotalHT($totalTHT);
                $devis->setTotalTTC($totalTTC);
                $devis->setIdClient($form->get('id_client')->getData());
                $devis->setUserCreate($this->getUser()->getNom());
                $devis->setStatut($form->get('statut')->getData());
                $devis->setTranches(1);
                $devis->setCreateAt(new \DateTime());
                $devis->setDateEcheance($form->get('date_echeance')->getData());
                foreach ($productList as $productData) {
                    $productId = $productData['id'];
                    $produit = $entityManager->getRepository(Produit::class)->find($productId);
                    if ($produit) {
                        $devisProduit = new DevisProduit();
                        $devisProduit->setProduit($produit);
                        $devisProduit->setQuantite($productData['quantite']); // Ajout de la quantité à DevisProduit
                        $devis->addDevisProduit($devisProduit);

                    }
                }
                $entityManager->persist($devis);
                $entityManager->flush();
                $session->clear();
                $this->addFlash("success","Votre devis a été créé avec succès");
                return $this->redirectToRoute('app_back_devis');

            }
        }

        return $this->render('back/devis/add.html.twig', [
            'form' => $form->createView(),
            'controller_name' => 'Ajouter un devis',
            'productList' => $productList,
            'totalTHT' => $totalTHT,
            'totalTTC' => $totalTTC
        ]);
    }

    #[Route('/admin/devis/remove_product/{type}/{id}/{numDevis?}', name: 'remove_product')]
    public function removeProduct(Request $request, $id, string $type,?string $numDevis, DevisProduitRepository $devisProduitRepository, DevisRepository $devisRepository, EntityManagerInterface $entityManager): Response
    {
        $session = $request->getSession();
        $productList = $session->get('productList', []);
        $produit_devis = $devisProduitRepository->findOneBy(['produit' => $id]);

        if ($type == "edit" && $produit_devis != null) {
            $entityManager->remove($produit_devis);
            $entityManager->flush();
            $devis = $devisRepository->findOneBy(['num_devis' => $produit_devis->getDevis()->getNumDevis()]);
            $devis_produits = $devisProduitRepository->findBy(['devis' => $devis]);

            $productList = [];
            foreach ($devis_produits as $devis_produit) {
                $produit = $devis_produit->getProduit();
                $tht = str_replace(',', '', $produit->getPrixUnitaire() * $devis_produit->getQuantite());
                $ttc = str_replace(',', '', $produit->getPrixUnitaire() * $devis_produit->getQuantite() * (1 + $produit->getTva() / 100));
                $productList[] = [
                    'id' => $produit->getId(),
                    'nom' => $produit->getNom(),
                    'numDevis' => $produit_devis->getDevis()->getNumDevis(),
                    'description' => $produit->getDescription(),
                    'quantite' => $devis_produit->getQuantite(),
                    'prix_unitaire' => $produit->getPrixUnitaire(),
                    'TVA' => $produit->getTva(),
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
        $this->addFlash("success", "Produit supprimé avec succès!");

        $session->set('productList', $productList);
        $totalTHT = 0;
        $totalTTC = 0;

        foreach ($productList as $product) {
            $totalTHT += floatval($product['THT']);
            $totalTTC += floatval($product['TTC']);
        }

        if ($type == "add") {
            return $this->redirectToRoute('add_devis', [
                'totalTHT' => $totalTHT,
                'totalTTC' => $totalTTC,
            ]);
        }


        if ($numDevis) {
            return $this->redirectToRoute('edit_devis', [
                'totalTHT' => $totalTHT,
                'totalTTC' => $totalTTC,
                'numDevis' => $numDevis,
            ]);
        }
    }

    #[Route('/admin/devis/get-product-info', name: 'get_product_info')]
    public function getProductInfo(Request $request,ProduitRepository $repository): JsonResponse
    {
        $productId = $request->get('product_id');
        $product = $repository->find($productId);

        $data = [];
        if ($product instanceof Produit) {
            $data['prix_unitaire'] = $product->getPrixUnitaire();
            $data['tva'] = $product->getTva();
        }

        return new JsonResponse($data);
    }





    #[Route('/admin/devis/generate_pdf/{numDevis}', name: 'generate_pdf')]
    public function generatePdf(EntityManagerInterface $entityManager, PDFService $PDFService, string $numDevis, EntrepriseRepository $entrepriseRepository, ClientRepository $clientRepository, DevisProduitRepository $devisProduitRepository,SessionInterface $session): Response
    {
        $devis = $entityManager->getRepository(Devis::class)->findOneBy(['num_devis' => $numDevis]);
        $clientInfo = $clientRepository->find($devis->getIdClient());
        $entrepriseInfo = $entrepriseRepository->find($this->getUser()->getIdEntreprise());
        $productList = $session->get('productList', []);

        $products = $devisProduitRepository->findBy(['devis' => $devis]);

        foreach ($products as $key => $product) {
            $prixUnitaire = $product->getProduit()->getPrixUnitaire();
            $quantite = $product->getQuantite();
            $tva = $product->getProduit()->getTVA();


            $tht = str_replace(',', '',number_format($prixUnitaire * $quantite, 2));

            // Calculate TTC
            $ttc =str_replace(',', '', number_format(($tht + ($tht * $tva / 100)), 2));

            // Add THT and TTC to the product array
            $products[$key]->THT = $tht;
            $products[$key]->TTC = $ttc;
            $products[$key]->prix_unitaire = $prixUnitaire;
        }

        $html = $this->render('back/devis/devis.html.twig', [
            'numDevis' => $devis->getNumDevis(),
            'dateCreation' => $devis->getCreateAt()->format('Y-m-d'),
            'totalTHT' => $devis->getTotalHT(),
            'totalTTC' => $devis->getTotalTTC(),
            'dateEcheance' => $devis->getDateEcheance()->format('Y-m-d'),
            'client' => $clientInfo,
            'productList' => $products,
            'Entreprise' => $entrepriseInfo
        ]);

        $PDFService->showPDF($html, $devis->getNumDevis());

        return new Response();

    }
    #[Route('/admin/devis/delete/{numDevis}', name: 'delete_devis')]
    public function deleteDevis(EntityManagerInterface $entityManager,string $numDevis): Response
    {
        $devis = $entityManager->getRepository(Devis::class)->findOneBy(['num_devis' => $numDevis]);

        if (!$devis) {
            throw $this->createNotFoundException('Devis non trouvé');
        }

        $entityManager->remove($devis);
        $entityManager->flush();

        $this->addFlash('success', 'Devis supprimé avec succès');

        return $this->redirectToRoute('app_back_devis');
    }


    #[Route('/admin/devis/edit/{numDevis}', name: 'edit_devis')]
    public function editDevis(Request $request, string $numDevis, DevisRepository $devisRepository, ProduitRepository $produitRepository, EntityManagerInterface $entityManager, SessionInterface $session, DevisProduitRepository $devisProduitRepository): Response
    {      $productList = $session->get('productList', []);
        $devis = $devisRepository->findOneBy(['num_devis' => $numDevis]);

        if(empty($productList)){
            $devis_produits = $devisProduitRepository->findBy(['devis' => $devis]);
            foreach ($devis_produits as $devis_produit) {
                $produit = $devis_produit->getProduit();

                $tht = str_replace(',', '', $produit->getPrixUnitaire() * $devis_produit->getQuantite());
                $ttc = str_replace(',', '', $produit->getPrixUnitaire() * $devis_produit->getQuantite() * (1 + $produit->getTva() / 100));
                $productList[] = [
                    'id' => $produit->getId(),
                    'nom' => $produit->getNom(),
                    'numDevis' => $numDevis,
                    'description' => $produit->getDescription(),
                    'quantite' => $devis_produit->getQuantite(),
                    'prix_unitaire' => $produit->getPrixUnitaire(),
                    'TVA' => $produit->getTva(),
                    'THT' => number_format($tht, 2),
                    'TTC' => number_format($ttc, 2)
                ];
            }
            $session->set('productList', $productList);
        }
        $form = $this->createForm(EditTypeForm::class);
        $form->handleRequest($request);

        $totalTHT = 0;
        $totalTTC = 0;

        if ($form->isSubmitted() && $form->isValid()) {

            if ($request->request->has('add_produit')) {
                $idProduit = $form->get('id_produit')->getData();
                $quantity = $form->get('quantite_disponible')->getData();
                if ($idProduit) {
                    $product = $produitRepository->find($idProduit);

                    if($quantity >= 1){
                        if ($product) {
                            $productList = $session->get('productList', []);

                            // Vérifier si le produit existe déjà dans la liste
                            $productExists = false;
                            foreach ($productList as &$item) {
                                if ($item['id'] === $product->getId()) {

                                    $item['quantite'] += $quantity;

                                    $tht = str_replace(',', '', $product->getPrixUnitaire() * $item['quantite']);
                                    $tcc = str_replace(',', '', $product->getPrixUnitaire() * $item['quantite'] * (1 + $product->getTva() / 100));
                                    $item['THT'] = number_format($tht, 2);
                                    $item['TTC'] = number_format($tcc, 2);

                                    $productExists = true;
                                    break;
                                }
                            }

                            if (!$productExists) {
                                // Ajouter le produit à la liste s'il n'existe pas déjà
                                $tht = str_replace(',', '', $product->getPrixUnitaire() * $quantity);
                                $tcc = str_replace(',', '', $product->getPrixUnitaire() * $quantity * (1 + $product->getTva() / 100));

                                $productList[] = [
                                    'id' => $product->getId(),
                                    'nom' => $product->getNom(),
                                    'prix_unitaire' => number_format($product->getPrixUnitaire(), 2),
                                    'quantite' => $quantity,
                                    'numDevis' => $numDevis,
                                    'THT' => number_format($tht, 2),
                                    'TVA' => $product->getTva(),
                                    'TTC' => number_format($tcc, 2)
                                ];
                            }

                            $session->set('productList', $productList);
                            $this->addFlash("success", "Produit ajouté avec succès!");
                        } else {
                            $this->addFlash("error", "Produit non trouvé dans la base de données.");
                        }
                    } else {
                        $this->addFlash("error", "La quantité doit être supérieure à zéro.");
                    }
                } else {
                    $this->addFlash("error", "Vous n'avez pas sélectionné de produit.");
                }
            }


            foreach ($productList as $product) {
                $totalTHT += floatval(str_replace(',', '', $product['THT']));
                $totalTTC += floatval(str_replace(',', '', $product['TTC']));
            }

            if ($request->request->has('create_devis')) {
                $devis->setTotalHT($totalTHT);
                $devis->setTotalTTC($totalTTC);
                $devis->setIdClient($form->get('id_client')->getData());
                $devis->setUserCreate($this->getUser()->getNom());
                $devis->setStatut($form->get('statut')->getData());
                $devis->setTranches(1);
                $devis->setCreateAt(new \DateTime());
                $devis->setDateEcheance($form->get('date_echeance')->getData());
                $entityManager->persist($devis);
                $entityManager->flush();
                foreach ($productList as $productData) {
                    $productId = $productData['id'];
                    $quantity = $productData['quantite'];
                    $existingDevisProduit = null;
                    foreach ($devis->getDevisProduits() as $devisProduit) {
                        if ($devisProduit->getProduit()->getId() === $productId) {
                            $existingDevisProduit = $devisProduit;
                            break;
                        }
                    }

                    if ($existingDevisProduit) {
                        if ($existingDevisProduit->getQuantite() !== $quantity) {
                            $existingDevisProduit->setQuantite($quantity);
                            $entityManager->persist($existingDevisProduit);
                            $entityManager->flush();

                        }
                    } else {
                        $produit = $entityManager->getRepository(Produit::class)->find($productId);
                        if ($produit) {
                            $devisProduit = new DevisProduit();
                            $devisProduit->setProduit($produit);
                            $devisProduit->setQuantite($quantity);
                            $devis->addDevisProduit($devisProduit);
                            $entityManager->persist($devis);
                            $entityManager->flush();
                        }
                    }
                }

                $session->clear();
                $this->addFlash("success", "Votre devis a été modifié avec succès");
                return $this->redirectToRoute('app_back_devis');
            }
        }

        return $this->render('back/devis/Edit.html.twig', [
            'form' => $form->createView(),
            'controller_name' => 'Modifier un devis',
            'productList' => $productList,
            'totalTHT' => $totalTHT,
            'totalTTC' => $totalTTC
        ]);
    }



}

