<?php

namespace App\Controller;

use App\Entity\Invoice;
use App\Form\User\SearchType;
use App\Model\SearchData;
use App\Repository\InvoiceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Attribute\IsGranted;
#[IsGranted('ROLE_COMPTABLE')]
class PaymentsController extends AbstractController
{
    #[Route('/admin/payments', name: 'app_back_payments')]
    public function getAllPayments(InvoiceRepository $invoiceRepository, Request $request, Security $security): Response
    {
        $user = $security->getUser();
        $userId = $user->getId();
        $userRole=$this->getUser()->getRoles();
        $companyId = $user->getCompanyId();

        $searchData = new SearchData();
        $formSearch = $this->createForm(SearchType::class, $searchData);
        $formSearch->handleRequest($request);

        if ($formSearch->isSubmitted() && $formSearch->isValid()) {
            $searchData = $formSearch->getData();
            $paymentsList = $invoiceRepository->getAllPaymentsBySearch($searchData,$request->query->getInt('page', 1),$userRole,$companyId);
        } else {
            $paymentsList = $invoiceRepository->getAllPayments($request->query->getInt('page', 1),$companyId);
        }



        return $this->render('back/payments/index.html.twig', [
            'controller_name' => 'PaymentsController',
            'paymentsList' => $paymentsList,
            'formSearch' => $formSearch,
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
