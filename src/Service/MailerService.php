<?php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class MailService
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendPasswordResetEmail(string $email, string $temporaryPassword): void
    {
        $message = (new Email())
            ->from('admin@whitepages.com')
            ->to($email)
            ->subject('Création de votre compte ContactsDirectory')
            ->text('Voici votre mot de passe temporaire : '.$temporaryPassword)
            ->html('<p>Voici votre mot de passe temporaire : <strong>'.$temporaryPassword.'</strong></p>');

        $this->mailer->send($message);
    }
}
