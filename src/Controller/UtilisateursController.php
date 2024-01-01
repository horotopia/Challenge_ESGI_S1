<?php

namespace App\Controller;

use App\Entity\Users;
use App\Form\utilisateurs\AddTypeForm;
use App\Form\utilisateurs\EditTypeForm;
use App\Form\utilisateurs\SearchTypeForm;
use App\model\SearchData;
use App\Repository\UsersRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
#[IsGranted('ROLE_ADMIN')]
class UtilisateursController extends AbstractController
{
    #[Route('/admin/utilisateurs', name: 'app_back_utilisateurs')]
    public function index(UsersRepository $usersRepository, Request $request): Response
    {
        $searchData = new SearchData();
        $form = $this->createForm(SearchTypeForm::class, $searchData);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $searchData = $form->getData();
            $users = $usersRepository->findBySearchData($searchData);
        } else {

            $users = $usersRepository->findUsersWithEntrepriseDetails($request->query->getInt('page', 1));
        }

        return $this->render('back/utilisateurs/index.html.twig', [
            'controller_name' => 'Utilisateurs',
            'form' => $form->createView(),
            'users' => $users,
        ]);
    }

    #[Route('/admin/user/add', name: 'add_user')]
    public function addUser(Request $request,EntityManagerInterface $entityManager,UserPasswordHasherInterface $hasher): Response
    {
        $user = new Users();
        $form = $this->createForm(AddTypeForm::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('password')->getData();
            $token = $hasher->hashPassword($user, $plainPassword);
            $user->setPassword($token);
            $user->setIsVerified(true);
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Utilisateur ajouté avec succès.');

            return $this->redirectToRoute('app_back_utilisateurs');
        }

        return $this->render('back/utilisateurs/Add_users.html.twig', [
            'user' => $user,
            'controller_name' => 'Ajouter un utilisateur',
            'form' => $form->createView(),
        ]);
    }



    #[Route('/admin/user/edit/{id}', name: 'edit_user')]
    public function editUser(Users $user, Request $request,EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EditTypeForm ::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setUpdatedAt(new \DateTime());
           $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash('success', 'Utilisateur modifié avec succès.');
            return $this->redirectToRoute('app_back_utilisateurs');
        }
        return $this->render('back/utilisateurs/Edit_users.html.twig', [
            'user' => $user,
            'controller_name' => 'Modifier un utilisateur',

            'form' => $form->createView(),
        ]);
    }



    #[Route('/admin/user/delete/{id}', name: 'delete_user')]
    public function deleteUser(Users $user, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($user);
        $entityManager->flush();

        $this->addFlash('success', 'Utilisateur supprimé avec succès.');

        return $this->redirectToRoute('app_back_utilisateurs');
    }


}
