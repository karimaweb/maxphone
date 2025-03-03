<?php

// src/Controller/RegistrationController.php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Entity\ActivationCode;
use App\Form\RegistrationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mime\Email;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher, MailerInterface $mailer): Response
    {
        $utilisateur = new Utilisateur();
        $form = $this->createForm(RegistrationType::class, $utilisateur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Vérification si l'utilisateur existe déjà
            $existingUtilisateur = $entityManager->getRepository(Utilisateur::class)->findOneBy(['email' => $utilisateur->getEmail()]);
            if ($existingUtilisateur) {
                $this->addFlash('danger', 'Cet email est déjà utilisé.');
                return $this->redirectToRoute('app_register');
            }

            // Hachage du mot de passe
            $hashedPassword = $passwordHasher->hashPassword($utilisateur, $utilisateur->getPassword());
            $utilisateur->setPassword($hashedPassword);

            // Persiste et enregistre l'utilisateur

            $entityManager->persist($utilisateur);
            $entityManager->flush();
            $entityManager->refresh($utilisateur);
        
    
            $activationCode = new ActivationCode();
            $activationCode->setUtilisateur($utilisateur);
            $activationCode->setCode(random_int(100000, 999999));
            $entityManager->persist($activationCode);
            $entityManager->flush();

            // Envoi du mail d'activation
            $email = (new Email())
                ->from('noreply@votre-site.com')
                ->to($utilisateur->getEmail())
                ->subject('Votre code d’activation')
                ->text("Votre code d'activation est : " . $activationCode->getCode());
            $mailer->send($email);

            $this->addFlash('success', 'Inscription réussie ! Un code d\'activation vous a été envoyé par email.');
            return $this->redirectToRoute('app_activation');
        }

        return $this->render('registration/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
