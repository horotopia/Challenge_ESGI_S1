<?php

namespace App\Controller;

use App\Entity\Company;
use App\Form\Company\EditType;
use App\Repository\QuoteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class CompanyController extends AbstractController
{
    #[Route('/our-company', name: 'app_company')]
    public function index(): Response
    {

        return $this->render('front/companies/index.html.twig', [
            'controller_name' => 'CompanyController',
        ]);
    }


    #[IsGranted('ROLE_ENTREPRISE')]
    #[Route('admin/companies/edit/{id}', name: 'app_back_companies_edit')]
    public function editCompany(Company $company, Request $request, EntityManagerInterface $entityManager, #[Autowire('%photo_dir%')] string $photoDir): Response
    {$form = $this->createForm(EditType ::class, $company);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($logo=$form['logo']->getData()){
              $filename=uniqid().'.'.$logo->guessExtension();
              $logo->move($photoDir,$filename);
            }
            $company->setLogo($filename);
            $entityManager->persist($company);
            $entityManager->flush();
            $this->addFlash('success', "L'enterprise modifié avec succès.");
            return $this->redirectToRoute('app_back_index');
        }

        return $this->render('back/companies/edit.html.twig', [
            'companies' => $company,
            'controller_name' => "Modifications d'une entreprise",
            'form' => $form->createView()]);
    }
}
