<?php

namespace App\Controller;

use App\Entity\Users;
use App\Form\User\AddType;
use App\Form\User\EditType;
use App\Form\User\SearchType;
use App\Model\SearchData;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
#[IsGranted('ROLE_ADMIN')]
class UsersController extends AbstractController
{
    #[Route('/admin/users', name: 'app_back_users')]
    public function index(UserRepository $usersRepository, Request $request): Response
    {
        $searchData = new SearchData();
        $form = $this->createForm(SearchType::class, $searchData);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $searchData = $form->getData();
            $users = $usersRepository->findBySearchData($searchData);
        } else {

            $users = $usersRepository->findUsersWithCompanyDetails($request->query->getInt('page', 1));
        }

        return $this->render('back/users/index.html.twig', [
            'controller_name' => 'index',
            'form' => $form->createView(),
            'users' => $users,
        ]);
    }

    #[Route('/admin/users/add', name: 'app_back_users_add')]
    public function addUser(Request $request,EntityManagerInterface $entityManager,UserPasswordHasherInterface $hasher): Response
    {
        $user = new Users();
        $form = $this->createForm(AddType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('password')->getData();
            $token = $hasher->hashPassword($user, $plainPassword);
            $user->setPassword($token);
            $user->setIsVerified(true);
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Utilisateur ajouté avec succès.');

            return $this->redirectToRoute('app_back_users');
        }

        return $this->render('back/users/add.html.twig', [
            'user' => $user,
            'controller_name' => 'addUser',
            'form' => $form->createView(),
        ]);
    }



    #[Route('/admin/users/edit/{id}', name: 'app_back_users_edit')]
    public function editUser(Users $user, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EditType ::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setUpdatedAt(new \DateTime());
           $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash('success', 'Utilisateur modifié avec succès.');
            return $this->redirectToRoute('app_back_users');
        }
        return $this->render('back/users/edit.html.twig', [
            'user' => $user,
            'controller_name' => 'editUser',

            'form' => $form->createView(),
        ]);
    }



    #[Route('/admin/user/delete/{id}', name: 'app_back_users_delete')]
    public function deleteUser(Users $user, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($user);
        $entityManager->flush();

        $this->addFlash('success', 'Utilisateur supprimé avec succès.');

        return $this->redirectToRoute('app_back_users');
    }


}
