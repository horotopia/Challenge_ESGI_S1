<?php

namespace App\Controller;

use App\Form\Contact\ContactType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact')]
    public function index(Request $request, MailerInterface $mailer): Response
    {
        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $contactFormData = $form->getData();

            $emailContent = 'Company : ' . $contactFormData['company'] . "\n" .
                'Full Name : ' . $contactFormData['fullName'] . "\n" .
                'Email : ' . $contactFormData['email'] . "\n" .
                'Phone : ' . $contactFormData['phone'] . "\n" .
                'Subject : ' . $contactFormData['subject'] . "\n" .
                'Message : ' . $contactFormData['message'];

            $messageToAdmin = (new Email())
                ->from('Fast Invoice <contact@fastinvoice.fr>')
                ->to('Fast Invoice <contact@fastinvoice.fr>')
                ->subject('Message depuis le formulaire de contact : ' . $contactFormData['subject'])
                ->text($emailContent);

            $mailer->send($messageToAdmin);

            $confirmationToClient = (new Email())
                ->from('Fast Invoice <contact@fastinvoice.fr>')
                ->to($contactFormData['email'])
                ->subject('Confirmation de réception de votre demande')
                ->text(
                    "Bonjour " . $contactFormData['fullName'] . ",\n\n" .
                    "Nous avons bien reçu votre demande avec les détails suivants :\n\n" . $emailContent . "\n\n" .
                    "Notre équipe va traiter votre demande dans les plus brefs délais.\n\n" .
                    "Cordialement,\n" .
                    "L'équipe"
                );

            $mailer->send($confirmationToClient);

            $this->addFlash('success', 'Votre message a bien été envoyé.');

            return $this->redirectToRoute('app_contact_success');
        }

        return $this->render('front/contact/index.html.twig', [
            'contact_form' => $form->createView(),
        ]);
    }
}
