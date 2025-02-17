<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\RegistrationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $utilisateur = new Utilisateur();  // ✅ Déclaration correcte
        $form = $this->createForm(RegistrationType::class, $utilisateur); 

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Vérifier si l'email existe déjà
            $existingUtilisateur = $entityManager->getRepository(Utilisateur::class)->findOneBy(['email' => $utilisateur->getEmail()]); // ✅ Correction ici
            if ($existingUtilisateur) {
                $this->addFlash('danger', 'Cet email est déjà utilisé.');
                return $this->redirectToRoute('app_register');
            }

            // Hash du mot de passe
            $hashedPassword = $passwordHasher->hashPassword($utilisateur, $utilisateur->getPassword());
            $utilisateur->setPassword($hashedPassword);

            // Définir le rôle par défaut
            $utilisateur->setRoles(['ROLE_USER']);

            // Sauvegarde de l'utilisateur
            $entityManager->persist($utilisateur); // ✅ Correction ici
            $entityManager->flush();

            $this->addFlash('success', 'Inscription réussie ! Connectez-vous maintenant.');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
