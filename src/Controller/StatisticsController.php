<?php

namespace App\Controller;

use App\Repository\ClientRepository;
use App\Repository\CompanyRepository;
use App\Repository\InvoiceRepository;
use App\Repository\ProductRepository;
use App\Service\PDFService;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
        $invoicesData = [];

        foreach ($invoiceTypesByMonth as $month => $data) {
            if (!empty($data)) {
                foreach ($data as $status => $count) {
                    $invoicesData[$month][$status] = $count;
                }
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
            'invoicesData' => $invoicesData,
             'revenueByMonth'=>$revenueByMonth
        ]);
    }


    #[Route('/admin/download/{type}', name: 'app_back_report_download')]
    public function downloadReport(string $type, ProductRepository $productRepository, CompanyRepository $companyRepository, InvoiceRepository $invoiceRepository, PDFService $PDFService, Request $request): Response {
        $companyId = $this->getUser()->getcompanyId();
        $companyInfo = $companyRepository->find($companyId);
        if ($type === 'salesByClient') {
            $data = $invoiceRepository->getSalesByClient($companyId);
            $view = 'back/reports/report_PdfSalesByCustomer.html.twig';
            $fileName = 'ventes_par_clients';
        } elseif ($type === 'salesByProduct') {
            $data = $invoiceRepository->getSalesByProduct($companyId);
            $view = 'back/reports/report_PdfSalesByProduct.html.twig';
            $fileName = 'ventes_par_produits';
        } else {
            throw $this->createNotFoundException('Type de rapport invalide');
        }

        $html = $this->renderView($view, [
            'controller_name' => 'StatisticsController',
            'data' => $data,
            'companyInfo' => $companyInfo
        ]);


        $PDFService->showPDF($html, $fileName);

            return new Response();
    }

}
