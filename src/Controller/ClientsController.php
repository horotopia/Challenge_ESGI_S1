<?php

namespace App\Controller;

use App\Entity\Client;
use App\Form\clients\editTypeForm;
use App\Form\clients\newTypeForm;
use App\Form\utilisateurs\SearchTypeForm;
use App\model\SearchData;
use App\Repository\ClientRepository;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\This;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
#[IsGranted('ROLE_ENTERPRISE')]
class ClientsController extends AbstractController
{
    #[Route('/admin/clients', name: 'app_back_clients')]
    public function index(ClientRepository $clientRepository,Request $request): Response
    {   $searchData = new SearchData();
        $form = $this->createForm(SearchTypeForm::class, $searchData);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $searchData = $form->getData();
            $clients = $clientRepository->findBySearchData($searchData);
        } else {

            $clients = $clientRepository->findclientsWithEntrepriseDetails($request->query->getInt('page', 1));
        }

        return $this->render('back/clients/index.html.twig', [
            'controller_name' => 'Clients',
            'form' => $form->createView(),
            'clients' => $clients,
        ]);

    }

    #[Route('/admin/client/add', name: 'add_client')]
    public function addClient(Request $request,EntityManagerInterface $entityManager): Response
    {
        $client = new Client();
        $form = $this->createForm(newTypeForm::class,$client);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $client->setUserCreate($this->getUser()->getNom());

            $entityManager->persist($client);
            $entityManager->flush();

            $this->addFlash('success', 'Client ajouté avec succès.');

            return $this->redirectToRoute('app_back_clients');
        }

        return $this->render('back/clients/Add_clients.html.twig', [
            'client' => $client,
            'controller_name' => 'Ajouter un client',
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/client/edit/{id}', name: 'edit_client')]
    public function editClient(Client $client, Request $request,EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(editTypeForm::class,$client );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $client->setUpdateAt(new \DateTime());
            $client->setUserUpdate($this->getUser()->getNom());
            $entityManager->persist($client);
            $entityManager->flush();
            $this->addFlash('success', 'Client modifié avec succès.');
            return $this->redirectToRoute('app_back_clients');
        }
        return $this->render('back/clients/Edit_clients.html.twig', [
            'clients' => $client,
            'controller_name' => 'Modifier un client',
            'form' => $form->createView(),
        ]);
    }



    #[Route('/admin/user/delete/{id}', name: 'delete_client')]
    public function deleteClient(Client $client, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($client);
        $entityManager->flush();

        $this->addFlash('success', 'Client supprimé avec succès.');

        return $this->redirectToRoute('app_back_clients');
    }







}
