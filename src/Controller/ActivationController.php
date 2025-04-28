<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Entity\ActivationCode;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ActivationController extends AbstractController
{
    #[Route('/activation', name: 'app_activation', methods: ['GET', 'POST'])]
    public function activateAccount(Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($request->isMethod('GET')) {
            return $this->render('activation/activation.html.twig');
        }

        // Récupère les données du formulaire
        $email = $request->request->get('email');
        $code = $request->request->get('code');

        // Vérifie si l'utilisateur existe
        $user = $entityManager->getRepository(Utilisateur::class)->findOneBy(['email' => $email]);

        if (!$user) {
            $this->addFlash('error', 'Utilisateur introuvable.');
            return $this->redirectToRoute('app_activation');
        }

        // Vérifie si un code d’activation existe pour ce user
        $activationCode = $entityManager->getRepository(ActivationCode::class)
            ->findOneBy(['utilisateur' => $user]);

        if (!$activationCode) {
            $this->addFlash('error', 'Votre compte est déjà activé.');
            return $this->redirectToRoute('app_login');
        }

        // Vérifie que le code est correct
        if ($activationCode->getCode() !== $code) {
            $this->addFlash('error', 'Code d’activation incorrect.');
            return $this->redirectToRoute('app_activation');
        }

        // Activation du compte : on ajoute ROLE_USER en base (même si getRoles() le fait en code)
        $user->setRoles(['ROLE_USER']);

        // Suppression du code d’activation
        $entityManager->persist($user);
        $entityManager->remove($activationCode);
        $entityManager->flush();

        $this->addFlash('success', 'Votre compte a été activé avec succès ! Vous pouvez maintenant vous connecter.');
        return $this->redirectToRoute('app_login');
    }
}
