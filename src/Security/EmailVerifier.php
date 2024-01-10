<?php

namespace App\Security;


use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;


class EmailVerifier
{
    public function __construct(
        private MailerInterface $mailer,
    )
    {

    }

    public function sendEmailConfirmation(string $verifyEmailRouteName,$data, TemplatedEmail $email): void
    {

        $context = $email->getContext();
        $context['routeName']=$verifyEmailRouteName;
        $context['user'] = $data['user'];
        $context['token'] = $data['token'];
        $context['lifeTimeToken'] =  $data['lifeTimeToken'];

        $email->context($context);

        $this->mailer->send($email);
    }


}
