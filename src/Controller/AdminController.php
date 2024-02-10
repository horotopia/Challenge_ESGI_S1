<?php

namespace App\Controller;

use App\Repository\CompanyRepository;
use App\Repository\QuoteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_back_index')]
    public function index(CompanyRepository $repository,QuoteRepository $quoteRepository): Response
    {
        $user = $this->getUser();
        $company = null;
        $companyId=$this->getUser()->getCompanyId();
        $userRole=$this->getUser()->getRoles();
        $pendingQuotes=$quoteRepository->countPendingQuotesForCurrentMonth($companyId,$userRole);
        $expiredQuotes=$quoteRepository->countExpiredQuotesForCurrentMonth($companyId,$userRole);

        if ($user) {
            $company = $repository->findBy(['id' => $user->getCompanyId()]);
        }

        return $this->render('back/companies/dashboard.html.twig', [
            'controller_name' => 'AdminController',
            'companies' => $company,
            'quotePendingData'=>$pendingQuotes,
            'quoteExpiredData'=>$expiredQuotes
        ]);
    }
}
