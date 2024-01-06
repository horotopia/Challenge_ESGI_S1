<?php

namespace App\Controller;

use App\Entity\Entreprise;
use App\Form\enterprise\EditTypeForm;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class EntrepriseController extends AbstractController
{
    #[Route('/entreprise', name: 'app_entreprise')]
    public function index(): Response
    {
        return $this->render('front/entreprise/index.html.twig', [
            'controller_name' => 'EntrepriseController',
        ]);
    }


    #[IsGranted('ROLE_ENTERPRISE')]
    #[Route('admin/entreprise/edit/{id}', name: 'edit_entreprise')]
    public function editEnterprise(Entreprise $entreprise,Request $request,EntityManagerInterface $entityManager,#[Autowire('%photo_dir%')] string $photoDir): Response
    {$form = $this->createForm(EditTypeForm ::class, $entreprise);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($logo=$form['logo']->getData()){
              $filename=uniqid().'.'.$logo->guessExtension();
              $logo->move($photoDir,$filename);
            }
            $entreprise->setLogo($filename);
            $entityManager->persist($entreprise);
            $entityManager->flush();
            $this->addFlash('success', "L'enterprise modifié avec succès.");
            return $this->redirectToRoute('app_back_index');
        }

        return $this->render('back/index/edit_enterptise.html.twig', [
            'entreprise' => $entreprise,
            'controller_name' => "Modifier l'enterprise",
            'form' => $form->createView()]);
    }
}
