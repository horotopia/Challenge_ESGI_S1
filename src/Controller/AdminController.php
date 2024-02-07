<?php

namespace App\Controller;

use App\Repository\CompanyRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_back_index')]
    public function index(CompanyRepository $repository): Response
    {
        $user = $this->getUser();
        $company = null;

        if ($user) {
            $company = $repository->findBy(['id' => $user->getCompanyId()]);
        }

        return $this->render('back/companies/dashboard.html.twig', [
            'controller_name' => 'AdminController',
            'companies' => $company,
        ]);
    }
}
