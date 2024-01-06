<?php

namespace App\Controller;

use App\Repository\EntrepriseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_back_index')]
    public function index(EntrepriseRepository $repository): Response
    {
        $user = $this->getUser();
        $entreprise = null;

        if ($user) {
            $entreprise = $repository->findBy(['id' => $user->getIdEntreprise()]);
        }

        return $this->render('back/index/index.html.twig', [
            'controller_name' => 'AdminController',
            'entreprises' => $entreprise,
        ]);
    }
}
