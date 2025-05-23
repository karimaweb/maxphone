<?php

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
            // Vérifie si l'email existe déjà
            $existingUtilisateur = $entityManager->getRepository(Utilisateur::class)->findOneBy(['email' => $utilisateur->getEmail()]);
            if ($existingUtilisateur) {
                $this->addFlash('danger', 'Cet email est déjà utilisé.');
                return $this->redirectToRoute('app_register');
            }

            // Hash du mot de passe
            $hashedPassword = $passwordHasher->hashPassword($utilisateur, $utilisateur->getPassword());
            $utilisateur->setPassword($hashedPassword);

            // Ajoute le rôle ROLE_USER
            $utilisateur->setRoles(['ROLE_USER']);

            // On NE connecte pas l'utilisateur ici pour forcer l'activation via code

            // Sauvegarde de l'utilisateur
            $entityManager->persist($utilisateur);
            $entityManager->flush();

            // Création du code d'activation
            $activationCode = new ActivationCode();
            $activationCode->setUtilisateur($utilisateur);
            $activationCode->setCode(random_int(100000, 999999));
            $entityManager->persist($activationCode);
            $entityManager->flush();

            // Envoi du mail avec le code
            $email = (new Email())
                ->from('noreply@votre-site.com')
                ->to($utilisateur->getEmail())
                ->subject('Votre code d’activation')
                ->text("Votre code d'activation est : " . $activationCode->getCode());
            $mailer->send($email);

            $this->addFlash('success', 'Inscription réussie ! Un code d\'activation vous a été envoyé par email.');

            // Redirige vers la page de saisie du code
            return $this->redirectToRoute('app_activation');
        }

        return $this->render('registration/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
