<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\EmailLog;
use App\Entity\EmailTemplate;
use App\Form\Email\EmailSendingType;
use App\Repository\EmailTemplateRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ENTREPRISE')]
class EmailController extends AbstractController
{
    #[Route('/admin/send-email', name: 'app_back_send_email')]
    public function sendEmail(Request $request, MailerInterface $mailer, EmailTemplateRepository $emailTemplateRepository, EntityManagerInterface $entityManager): Response
    {
        $companyId = $this->getUser()->getCompanyId()->getId();
        $form = $this->createForm(EmailSendingType::class, null, [
            'company_id' => $companyId
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $emailTemplate = $emailTemplateRepository->find($data['emailTemplate']);

            if (isset($data['recipient']['client']) && $data['recipient']['client'] instanceof Client) {
                $recipient = $data['recipient']['client']->getEmail();
            } elseif (isset($data['recipient']['email'])) {
                $recipient = $data['recipient']['email'];
            } else {
                $recipient = 'ilyesse@bhgroupe.com';
            }

            $email = (new Email())
                ->from($this->getUser()->getEmail())
                ->to($recipient)
                ->subject($emailTemplate->getName())
                ->html($emailTemplate->getContentBeforeButtons() . $data['message'] . $emailTemplate->getContentAfterButtons());

            foreach ($data['attachments'] as $attachment) {
                $attachmentPath = $attachment->getRealPath();
                $email->attachFromPath($attachmentPath);
            }

            $mailer->send($email);
            $this->addFlash('success', 'L\'e-mail a été envoyé avec succès.');

            $emailLog = new EmailLog();
            $emailLog->setSender($this->getUser()->getEmail());
            $emailLog->setReceiver($recipient);
            $emailLog->setSubject($emailTemplate->getName());
            $emailLog->setContent($emailTemplate->getContentBeforeButtons() . $data['message'] . $emailTemplate->getContentAfterButtons());
            $emailLog->setStatus('Envoyé');
            $emailLog->setSentAt(new \DateTime());

            $entityManager->persist($emailLog);
            $entityManager->flush();

            return $this->redirectToRoute('app_back_email_tracking');
        }

        return $this->render('back/email/send_email.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}