<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class MailController extends AbstractController
{
    #[Route('/send-test-email', name: 'send_test_email')]
    public function sendEmail(MailerInterface $mailer): Response
    {
        try {
            $email = (new Email())
                ->from('ouchenekarima2008@hotmail.com')
                ->to('test@mailtrap.io') // Utiliser Mailtrap pour tester
                ->subject('Test Symfony Mailer')
                ->text('Ceci est un test d’envoi d’email avec Symfony Mailer.');

            $mailer->send($email);

            return new Response('<p style="color:green;">✅ Email envoyé avec succès ! Vérifie Mailtrap.</p>');
        } catch (TransportExceptionInterface $e) {
            return new Response('<p style="color:red;">❌ Erreur lors de l\'envoi : ' . $e->getMessage() . '</p>');
        }
    }
}
