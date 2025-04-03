<?php
// src/Controller/SecurityController.php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\JsonResponse;
class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
       
    

        // Récupérer l'erreur de connexion s'il y en a une
        $error = $authenticationUtils->getLastAuthenticationError();
        // Dernier nom d'utilisateur saisi
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        // Cette méthode sera interceptée par Symfony
    }
    #[Route('/user/status', name: 'user_status')]
    public function userStatus(): JsonResponse
    {
    return new JsonResponse(['loggedIn' => $this->getUser() !== null]);
    }
    /**
     * @Route("/rendezvous/annuler", name="rendezvous_annuler", methods={"POST"})
     */
    public function annulerRendezVous(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $id = $data['id'];

        $rendezVous = $entityManager->getRepository(RendezVous::class)->find($id);

        if (!$rendezVous) {
            return new JsonResponse(['message' => 'Rendez-vous introuvable !'], 404);
        }

        // Logique d'annulation (par exemple, modifier le statut du rendez-vous)
        $rendezVous->setStatutRendezVous('annulé'); // Assurez-vous d'avoir un statut 'annulé' dans votre modèle
        $entityManager->flush();

        return new JsonResponse(['message' => 'Rendez-vous annulé avec succès !']);
    }
}
