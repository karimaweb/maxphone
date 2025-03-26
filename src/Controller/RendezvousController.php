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
    // Méthode pour générer les créneaux mensuels 
    public function genererCreneaux(EntityManagerInterface $em): Response
    {
        $joursAutorises = ['Wednesday', 'Friday']; //  Seuls Mercredi et Vendredi
    
        $now = new \DateTime(); //  Date actuelle
        $dateFin = (clone $now)->modify('last day of +1 months'); //  Génération jusqu'à la fin du mois suivant
    
        $compteur = 0;
    
        while ($now <= $dateFin) {
            $jourSemaine = $now->format('l'); //  Format complet du jour (Wednesday, Friday, etc.)
    
            if (in_array($jourSemaine, $joursAutorises, true)) {
                //  Créneaux de 14:00 à 18:00 par intervalles de 20 minutes
                $debut = new \DateTime($now->format('Y-m-d') . ' 14:00');
                $fin = new \DateTime($now->format('Y-m-d') . ' 18:00');
    
                while ($debut <= $fin) {
                    $dateCreneau = clone $debut; //  Clone pour éviter les modifications indésirables
    
                    //  Vérifier si ce créneau existe déjà
                    $existant = $em->getRepository(RendezVous::class)->findOneBy([
                        'dateHeureRendezVous' => $dateCreneau
                    ]);
    
                    if (!$existant) {
                        $rdv = new RendezVous();
                        $rdv->setDateHeureRendezVous($dateCreneau);
                        $rdv->setDescription('Créneau libre');
                        $rdv->setStatutRendezVous('disponible');
    
                        $em->persist($rdv);
                        $compteur++;
                    }
    
                    $debut->modify('+20 minutes'); //  Avance de 20 minutes
                }
            }
    
            $now->modify('+1 day'); // 🔹 Passe au jour suivant
        }
    
        $em->flush();
        return new Response("$compteur créneaux de 20 minutes générés sur 2 mois !");
    }
    
        
    #[Route('_api/rdv', name: 'rendezvous_api_rdv')]

    public function getCreneaux(RendezVousRepository $rendezVousRepository): JsonResponse

    {
        $rdvs = $rendezVousRepository->findAll();
        $rdvs = array_filter($rdvs, function($rdv) {
            return !in_array($rdv->getDateHeureRendezVous()->format('l'), ['Thursday']);// créneau en plus

    });
    if (!$rdvs) {
        return new JsonResponse([], Response::HTTP_OK);
    }

        $events = [];
        foreach ($rdvs as $rdv) {
        // Vérifier le statut et changer la couleur en fonction
            $statut = $rdv->getStatutRendezVous();
            $color = $statut === "réservé" ? "red" : "green";
            $title = $statut === "réservé" ? " réservé" : "disponible";

        $events[] = [
            'id' => $rdv->getId(),
            'title' => $title,
            'start' => $rdv->getDateHeureRendezVous()->format('Y-m-d H:i:s'),
            'color' => $color,
            'statut' => $statut, //  Ajout du statut
        ];
    }
    
    return new JsonResponse($events);
    }
    #[Route('/reserver', name: 'rendezvous_reserver', methods: ['POST'])]
    public function reserver(
        Request $request, 
        RendezVousRepository $rendezVousRepository, 
        EntityManagerInterface $entityManager
    ): JsonResponse {
        // 1) Vérifier la connexion de l'utilisateur
        if (!$this->getUser()) {
            return new JsonResponse(['message' => 'Vous devez être connecté pour réserver un créneau.'], 403);
        }
    
        // 2) Vérifier si l'utilisateur a déjà un rendez-vous "réservé"
        $user = $this->getUser();
        $existingRdv = $rendezVousRepository->findOneBy([
            'utilisateur' => $user,
            'statutRendezVous' => 'réservé'
        ]);
        if ($existingRdv) {
            return new JsonResponse(['message' => 'Vous avez déjà un rendez-vous réservé.'], 400);
        }
    
        // 3) Récupérer et décoder le JSON du body
        $data = json_decode($request->getContent(), true);
        if (!isset($data['id'])) {
            return new JsonResponse(['message' => 'ID du rendez-vous manquant.'], 400);
        }
    
        // 4) Trouver le rendez-vous correspondant
        $rendezVous = $rendezVousRepository->find($data['id']);
        if (!$rendezVous) {
            return new JsonResponse(['message' => 'Rendez-vous introuvable.'], 404);
        }
    
        // 5) Vérifier si ce créneau est déjà réservé
        if ($rendezVous->getStatutRendezVous() === 'réservé') {
            return new JsonResponse(['message' => 'Ce créneau est déjà réservé.'], 400);
        }
    
        // 6) Réserver le créneau pour l'utilisateur connecté
        $rendezVous->setStatutRendezVous('réservé');
        $rendezVous->setUtilisateur($user);
    
        // 7) Enregistrer en base
        $entityManager->persist($rendezVous);
        $entityManager->flush();
    
        // 8) Réponse de succès
        return new JsonResponse(['message' => 'Créneau réservé avec succès !'], 200);
    }
    
// Dans votre contrôleur (par exemple RendezVousCrudController)
    public function annulerRendezVous(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $id = $data['id']; // Vous récupérez l'ID du rendez-vous que vous souhaitez annuler

        $rendezVous = $entityManager->getRepository(RendezVous::class)->find($id);

        if (!$rendezVous) {
        return new JsonResponse(['message' => 'Rendez-vous introuvable !'], 404);
    }

    // Modifiez ici la logique d'annulation, par exemple changer le statut à 'annulé'
    $rendezVous->setStatutRendezVous('annulé');
    $entityManager->flush(); // Sauvegarder le changement dans la base de données

    return new JsonResponse(['message' => 'Rendez-vous annulé avec succès !']);
    }

    #[Route('/annuler', name: 'rendezvous_annuler', methods: ['POST'])]
    public function annuler(
    Request $request,
    RendezVousRepository $rendezvousRepository,
    EntityManagerInterface $em
    ): JsonResponse {
    $data = json_decode($request->getContent(), true);

    // 1) Vérifier la présence de l'ID
    if (!isset($data['id'])) {
        return new JsonResponse([
            'status' => 'error',
            'message' => 'ID du rendez-vous non spécifié.'
        ], 400);
    }

    // 2) Récupérer le rendez-vous
    $rdv = $rendezvousRepository->find($data['id']);
    if (!$rdv) {
        return new JsonResponse([
            'status' => 'error',
            'message' => 'Rendez-vous introuvable.'
        ], 404);
    }

    // 3) Vérifier la date (pas passé)
    $now = new \DateTime();
    if ($rdv->getDateHeureRendezVous() < $now) {
        return new JsonResponse([
            'status' => 'error',
            'message' => 'Impossible d’annuler un rendez-vous passé.'
        ], 400);
    }

    // 4) Vérifier le statut (bien "réservé")
    if ($rdv->getStatutRendezVous() !== 'réservé') {
        return new JsonResponse([
            'status' => 'error',
            'message' => 'Ce rendez-vous n’est pas marqué comme réservé.'
        ], 400);
    }

    // 5) Mettre à jour le statut pour l’annuler
    $rdv->setStatutRendezVous('disponible');

    // 6) Sauvegarder en base
    $em->persist($rdv);
    $em->flush();

    // 7) Retourner la réponse
    return new JsonResponse([
        'status' => 'success',
        'message' => 'Le rendez-vous a été annulé avec succès.'
    ]);
    }
}

