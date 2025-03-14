<?php

namespace App\Controller;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use App\Repository\RendezVousRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\RendezVous;
use Symfony\Component\HttpFoundation\Request;



#[Route('/rendezvous', name: 'rendezvous_')]
class RendezvousController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(RendezvousRepository $rendezvousRepository): Response
    {
        $rendezvous = $rendezvousRepository->findAll();
        return $this->render('rendezvous/index.html.twig', [
            'rendezvous' => $rendezvous,
        ]);
    }
    #[Route('/generer-creneaux', name: 'generer_creneaux')]
 
    public function genererCreneaux(EntityManagerInterface $em): Response
    {
        $joursAutorises = ['Wednesday', 'Friday']; // 🟢 Seuls Mercredi et Vendredi
        $heures = ['14:00', '15:00', '16:00', '17:00'];
    
        $now = new \DateTime(); // 📅 Date actuelle
        $dateFin = (clone $now)->modify('+2 months'); // 🔹 Générer sur **2 mois**
    
        $compteur = 0;
    
        while ($now <= $dateFin) {
            $jourSemaine = $now->format('l'); // Ex: "Wednesday", "Thursday", etc.
    
            // 🔹 Vérifier que ce n'est QUE mercredi ou vendredi (PAS jeudi !)
            if (in_array($jourSemaine, $joursAutorises, true)) {
                foreach ($heures as $heure) {
                    $dateCreneau = new \DateTime($now->format('Y-m-d') . " " . $heure);
    
                    // 🔍 Vérifier si ce créneau existe déjà dans la base de données
                    $existant = $em->getRepository(RendezVous::class)->findOneBy([
                        'dateHeureRendezVous' => $dateCreneau
                    ]);
    
                    if (!$existant) {
                        $rdv = new RendezVous();
                        $rdv->setDateHeureRendezVous($dateCreneau);
                        $rdv->setDescription('Créneau libre');
                        $rdv->setStatutRendezVous('disponible'); // 🔹 Assurer que tous les créneaux ont un statut
    
                        $em->persist($rdv);
                        $compteur++;
                    }
                }
            }
            $now->modify('+1 day'); // 🔹 Passer au jour suivant proprement
        }
    
        $em->flush();

    
        return new Response("✅ $compteur créneaux générés avec succès sur **2 mois** !");
    }
    
        
    #[Route('_api/rdv', name: 'rendezvous_api_rdv')]
    public function getCreneaux(RendezVousRepository $rendezVousRepository): JsonResponse
{
    $rdvs = $rendezVousRepository->findAll();
    $rdvs = array_filter($rdvs, function($rdv) {
        return !in_array($rdv->getDateHeureRendezVous()->format('l'), ['Thursday']);
    });

    if (!$rdvs) {
        return new JsonResponse([], Response::HTTP_OK);
    }

    $events = [];
    foreach ($rdvs as $rdv) {
        // Vérifier le statut et changer la couleur en fonction
        $statut = $rdv->getStatutRendezVous();
        $color = $statut === "réservé" ? "red" : "green";
        $title = $statut === "réservé" ? "Créneau réservé" : "Créneau disponible";

        $events[] = [
            'id' => $rdv->getId(),
            'title' => $title,
            'start' => $rdv->getDateHeureRendezVous()->format('Y-m-d H:i:s'),
            'color' => $color,
            'statut' => $statut, // 🟢 Ajout du statut
        ];
    }
    dump($events); // Ajoute ceci pour voir les créneaux envoyés par l'API
    return new JsonResponse($events);
}
    #[Route('/reserver', name: 'rendezvous_reserver', methods: ['POST'])]
    public function reserver(Request $request, RendezVousRepository $rendezVousRepository, EntityManagerInterface $entityManager): JsonResponse
{
    $data = json_decode($request->getContent(), true);
    $rendezVous = $rendezVousRepository->find($data['id']);

    if (!$rendezVous) {
        return new JsonResponse(['message' => 'Créneau introuvable'], 404);
    }

    // Vérifie si déjà réservé
    if ($rendezVous->getStatutRendezVous() === 'réservé') {
        return new JsonResponse(['message' => 'Ce créneau est déjà réservé.'], 400);
    }

    // Modifier le statut
    $rendezVous->setStatutRendezVous('réservé');
    $rendezVous->setUtilisateur($this->getUser()); // Associe l'utilisateur connecté
    $entityManager->persist($rendezVous);
    $entityManager->flush();

    return new JsonResponse(['message' => 'Créneau réservé avec succès']);
}
    
}

