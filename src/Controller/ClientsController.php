<?php

namespace App\Controller;

use App\Entity\Client;
use App\Form\Client\editType;
use App\Form\Client\newType;
use App\Form\User\SearchType;
use App\Model\SearchData;
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
        $form = $this->createForm(SearchType::class, $searchData);
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

    #[Route('/admin/clients/add', name: 'app_back_clients_add')]
    public function addClient(Request $request,EntityManagerInterface $entityManager): Response
    {
        $client = new Client();
        $form = $this->createForm(newType::class,$client);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $client->setUserCreated($this->getUser()->getId());

            $entityManager->persist($client);
            $entityManager->flush();

            $this->addFlash('success', 'Client ajouté avec succès.');

            return $this->redirectToRoute('app_back_clients');
        }

        return $this->render('back/clients/add.html.twig', [
            'client' => $client,
            'controller_name' => 'addClient',
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/clients/edit/{id}', name: 'app_back_clients_edit')]
    public function editClient(Client $client, Request $request,EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(editType::class,$client );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $client->setUpdatedAt(new \DateTime());
            $client->setUserUpdated($this->getUser()->getId());
            $entityManager->persist($client);
            $entityManager->flush();
            $this->addFlash('success', 'Client modifié avec succès.');
            return $this->redirectToRoute('app_back_clients');
        }
        return $this->render('back/clients/edit.html.twig', [
            'clients' => $client,
            'controller_name' => 'editClient',
            'form' => $form->createView(),
        ]);
    }



    #[Route('/admin/clients/delete/{id}', name: 'app_back_clients_delete')]
    public function deleteClient(Client $client, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($client);
        $entityManager->flush();

        $this->addFlash('success', 'Client supprimé avec succès.');

        return $this->redirectToRoute('app_back_clients');
    }







}
