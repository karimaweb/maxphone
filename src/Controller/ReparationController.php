<?php
namespace App\Controller;

use App\Entity\HistoriqueReparation;
use App\Repository\ReparationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\ReparationType;
use App\Entity\Reparation;

#[Route('/reparation', name: 'reparation_')]
class ReparationController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(ReparationRepository $reparationRepository): Response
    {
        $reparations = $reparationRepository->findAll();
        return $this->render('reparation/index.html.twig', [
            'reparations' => $reparations,
        ]);
    }

    #[Route('/create', name: 'create')]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $reparation = new Reparation();
        $form = $this->createForm(ReparationType::class, $reparation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($reparation);
            $entityManager->flush();

            return $this->redirectToRoute('reparation_index'); // ✅ Correction de la redirection
        }

        return $this->render('reparation/create.html.twig', [ // ✅ Correction du chemin du template
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/update', name: 'reparation_update', methods: ['POST'])]
public function updateReparationStatus(Reparation $reparation, Request $request, EntityManagerInterface $entityManager): Response
{
    if (!$reparation) {
        return $this->json(['message' => 'Réparation non trouvée.'], 404);
    }

    $data = json_decode($request->getContent(), true);
    $nouveauStatut = $data['statut'] ?? null;
    $commentaire = $data['commentaire'] ?? '';

    if (!$nouveauStatut) {
        return $this->json(['message' => 'Statut manquant.'], 400);
    }

    if ($reparation->getStatutReparation() !== $nouveauStatut) {
        $historique = new HistoriqueReparation();
        $historique->setReparation($reparation);
        $historique->setStatutHistoriqueReparation($nouveauStatut);
        $historique->setCommentaire($commentaire);
        $historique->setDateMajReparation(new \DateTime());

        $entityManager->persist($historique);
        $reparation->setStatutReparation($nouveauStatut);
        $entityManager->persist($reparation);
        $entityManager->flush();

        return $this->json(['message' => 'Statut mis à jour avec historique enregistré.']);
    }

    return $this->json(['message' => 'Aucune modification détectée.']);
}
}