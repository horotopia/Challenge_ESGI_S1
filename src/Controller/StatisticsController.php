<?php

namespace App\Controller;

use App\Repository\ClientRepository;
use App\Repository\InvoiceRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
#[IsGranted('ROLE_COMPTABLE')]
class StatisticsController extends AbstractController
{
    #[Route('/admin/statistics', name: 'app_back_statistics')]
    public function index(ProductRepository $productRepository,ClientRepository $clientRepository ,InvoiceRepository $invoiceRepository): Response
    {
        $companyId = $this->getUser()->getcompanyId();

        $numberOfProductsAddedToday = $productRepository->countProductsAddedToday($companyId);
        $numberOfClientsAddedToday = $clientRepository->countClientsAddedToday($companyId);
        $numberOfSalesAddedToday = $invoiceRepository->countSalesAddedToday($companyId ,'Payé'); // Nouvelle méthode

        $totalNumberOfSales = $invoiceRepository->countTotalSales( $companyId,'Payé');
        $totalNumberOfProducts = $productRepository->count(['companyId' => $companyId]);
        $totalNumberOfClients = $clientRepository->count(['companyId' => $companyId]);

        $productsPercentageAddedToday = ($totalNumberOfProducts > 0) ? ($numberOfProductsAddedToday / $totalNumberOfProducts * 100) : 0;
        $clientsPercentageAddedToday = ($totalNumberOfClients > 0) ? ($numberOfClientsAddedToday / $totalNumberOfClients * 100) : 0;
        $salesPercentageAddedToday = ($totalNumberOfSales > 0) ? ($numberOfSalesAddedToday / $totalNumberOfSales * 100) : 0;

        $invoiceTypesByMonth=$invoiceRepository->getInvoiceTypesByMonth($companyId);

        $monthsLabels = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
        $invoicesData = [];

        foreach ($invoiceTypesByMonth as $month => $data) {
            foreach ($data as $status => $count) {
                $invoicesData[$month][$status] = $count;
            }
        }

        $revenueByMonth=$invoiceRepository->getRevenueByMonth($companyId);

        return $this->render('back/statistics/index.html.twig', [
            'controller_name' => 'StatisticsController',
            'totalNumberOfProducts'=>$totalNumberOfProducts,
            'totalNumberOfClients'=>$totalNumberOfClients,
            'totalNumberOfSales'=>$totalNumberOfSales,
            'productsPercentageAddedToday' => $productsPercentageAddedToday,
            'clientsPercentageAddedToday' => $clientsPercentageAddedToday,
            'salesPercentageAddedToday'=>$salesPercentageAddedToday,
            'monthsLabels' => $monthsLabels,
            'invoicesData' => $invoicesData,
             'revenueByMonth'=>$revenueByMonth
        ]);
    }
}
