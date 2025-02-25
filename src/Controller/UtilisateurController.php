<?php

namespace App\Controller;
use App\Entity\Utilisateur;
use App\Entity\RendezVous;
use App\Entity\Reparation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\UtilisateurType;
use Symfony\Component\HttpFoundation\Request;



class UtilisateurController extends AbstractController
{
    #[Route('/admin/utilisateur/{id}', name: 'admin_utilisateur_detail')]
    public function utilisateurDetail(Utilisateur $utilisateur, EntityManagerInterface $entityManager): Response
    {
        // Récupérer les rendez-vous et réparations associés
        $rendezVous = $entityManager->getRepository(RendezVous::class)->findBy(['utilisateur' => $utilisateur]);
        $reparations = $entityManager->getRepository(Reparation::class)->findBy(['utilisateur' => $utilisateur]);

        return $this->render('utilisateur/utilisateur_detail.html.twig', [
            'utilisateur' => $utilisateur,
            'rendezVous' => $rendezVous,
            'reparations' => $reparations,
        ]);
    }
        // Route pour créer un utilisateur
        #[Route('/utilisateur/create', name: 'admin_utilisateur_create')]
public function createUtilisateur(Request $request, EntityManagerInterface $entityManager): Response
{
    $utilisateur = new Utilisateur(); // Ne pas récupérer un utilisateur inexistant
    $form = $this->createForm(UtilisateurType::class, $utilisateur);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager->persist($utilisateur);
        $entityManager->flush();

        return $this->redirect('reparation/create.html.twig');
    }
    

    return $this->render('utilisateur/create.html.twig', [
        'form' => $form->createView(),
    ]);
}

}
