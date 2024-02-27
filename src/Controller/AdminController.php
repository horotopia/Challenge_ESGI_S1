<?php

namespace App\Controller;

use App\Repository\CompanyRepository;
use App\Repository\InvoiceRepository;
use App\Repository\QuoteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_back_index')]
    public function index(CompanyRepository $repository,QuoteRepository $quoteRepository,InvoiceRepository $invoiceRepository): Response
    {
        $user = $this->getUser();
        $company = null;
        $companyId=$this->getUser()->getCompanyId();
        $userRole=$this->getUser()->getRoles();
        $pendingQuotes=$quoteRepository->countPendingQuotesForCurrentMonth($companyId,$userRole);
        $expiredQuotes=$quoteRepository->countExpiredQuotesForCurrentMonth($companyId,$userRole);
        $OverdueInvoices=$invoiceRepository->getOverdueInvoicesSummary($companyId);
        $PaidInvoices=$invoiceRepository->getPaidInvoicesSummary($companyId);
        $UnpaidInvoices=$invoiceRepository->getUnpaidInvoicesSummary($companyId);

        $revenueOnFiscalYear = $invoiceRepository->calculateRevenueOnFiscalYear($companyId);
        $revenueOfPreviousFiscalYear=$invoiceRepository->calculateRevenueOfPreviousFiscalYear($companyId);
        $revenueByMonth = $invoiceRepository->calculateRevenueByMonth($companyId);
        $revenuePreviousMonth=$invoiceRepository->calculateRevenuePreviousMonth($companyId);
        $revenueByDay = $invoiceRepository->calculateRevenueByDay($companyId);

        if ($revenueOfPreviousFiscalYear != 0) {
            $variationPercentageYear = number_format((($revenueOnFiscalYear - $revenueOfPreviousFiscalYear) / $revenueOfPreviousFiscalYear) * 100, 2);
        } else {
            $variationPercentageYear = "N/A";
        }

        if ($revenuePreviousMonth != 0) {
            $variationPercentageMonth = number_format((($revenueByMonth - $revenuePreviousMonth) / $revenuePreviousMonth) * 100, 2);
        } else {
            $variationPercentageMonth = "N/A";
        }

        $variationYear = number_format($revenueOnFiscalYear - $revenueOfPreviousFiscalYear, 2);

        $variationMonth = number_format($revenueByMonth - $revenuePreviousMonth, 2);

        if ($user) {
            $company = $repository->findBy(['id' => $user->getCompanyId()]);
        }
        return $this->render('back/companies/dashboard.html.twig', [
            'controller_name' => 'AdminController',
            'companies' => $company,
            'quotePendingData'=>$pendingQuotes,
            'quoteExpiredData'=>$expiredQuotes,
            'overdueInvoices'=>$OverdueInvoices,
            'paidInvoices'=>$PaidInvoices,
            'unpaidInvoices'=>$UnpaidInvoices,
            'revenueOnFiscalYear'=>$revenueOnFiscalYear,
            'revenueByMonth'=>$revenueByMonth,
            'revenueByDay'=>$revenueByDay,
            'variationPercentageYear'=>$variationPercentageYear,
            'variationYear'=>$variationYear,
            'variationPercentageMonth'=>$variationPercentageMonth,
            'variationMonth'=>$variationMonth
        ]);
    }
}
