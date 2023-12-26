<?php

namespace App\Controller;

use App\Entity\Users;
use App\Form\RegistrationFormType;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class RegistrationController extends AbstractController
{
    private EmailVerifier $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }

    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager,TokenGeneratorInterface $tokenGenerator): Response
    {
        $user = new Users();
        $form = $this->createForm(RegistrationFormType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //generate token
            $tokenRegistration= $tokenGenerator->generateToken();
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $user->setTelephone( $form->get('telephone')->getData());
            $user->setTokenRegistration($tokenRegistration);
            $entreprise = $form->get('id_entreprise')->getData();
            $entityManager->persist($entreprise);
            $entityManager->persist($user);
            $entityManager->flush();

            // generate a signed url and email it to the user
            $this->emailVerifier->sendEmailConfirmation('app_verify_email',
                ['user' => $user,
                    'token' => $tokenRegistration,
                    'lifeTimeToken' => $user->getTokenRegistrationLifeTime()->format('d-m-H-i-s')
                ],
                (new TemplatedEmail())
                    ->from(new Address('ali.khelifa@se.univ-bejaia.dz'))
                    ->to($user->getEmail())
                    ->subject('Please Confirm your Email')
                    ->htmlTemplate('front/registration/confirmation_email.html.twig')
            );

            // do anything else you need here, like send an email
            return $this->redirectToRoute('app_preview_email',["email"=>$user->getEmail()]);
        }

        return $this->render('front/registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/verify/email/{token}/{id<\d+>}', name: 'app_verify_email')]
    public function verifyUserEmail(string $token, Users $user, EntityManagerInterface $entityManager): Response
    {
        if ($user->getTokenRegistration() !== $token) {
            throw new AccessDeniedException();
        }

        if ($user->getTokenRegistration() === null) {
            throw new AccessDeniedException();
        }
        if (new \DateTime('now') > $user->getTokenRegistrationLifeTime()) {

            $this->addFlash('error', "Désolé, le lien que vous avez utilisé pour activer votre compte a expiré. Veuillez vous reconnecter ou vous réinscrire pour obtenir un nouveau lien d'activation.!.");

            return $this->redirectToRoute('app_login');
        }
        $user->setIsVerified(true);
        $user->setTokenRegistration(null);
        $entityManager->flush();

        $this->addFlash('success', 'Votre adresse e-mail a été vérifiée. Vous pouvez vous connecter✅!');

        return $this->redirectToRoute('app_login');


    }
}
