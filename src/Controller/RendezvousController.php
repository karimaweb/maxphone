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
    // afficher la liste des rendez vous
    public function index(RendezvousRepository $rendezvousRepository): Response
    {
        $rendezvous = $rendezvousRepository->findAll();
        return $this->render('rendezvous/index.html.twig', [
            'rendezvous' => $rendezvous,
        ]);
    }
    //générer des créneaux disponibles
    #[Route('/generer-creneaux', name: 'generer_creneaux')]
    // Méthode pour générer les créneaux mensuels 

    public function genererCreneaux(EntityManagerInterface $em): Response
    {
        $joursAutorises = ['Wednesday', 'Friday'];
        $now = new \DateTime();
        $debutPeriode = (clone $now)->setTime(0, 0);
        $dateFin = (clone $now)->modify('last day of +1 months')->setTime(23, 59);
    
        // 1. Supprimer les anciens créneaux dans la période
        $ancienCreneaux = $em->getRepository(RendezVous::class)->createQueryBuilder('r')
            ->where('r.dateHeureRendezVous BETWEEN :start AND :end')
            ->setParameter('start', $debutPeriode)
            ->setParameter('end', $dateFin)
            ->getQuery()
            ->getResult();
    
        foreach ($ancienCreneaux as $rdv) {
            $em->remove($rdv);
        }
    
        $em->flush(); // Flush après suppression
    
        // 2. Générer les nouveaux créneaux
        $compteur = 0;
        $jour = clone $debutPeriode;
    
        while ($jour <= $dateFin) {
            $jourSemaine = $jour->format('l');
    
            if (in_array($jourSemaine, $joursAutorises, true)) {
                $debut = new \DateTime($jour->format('Y-m-d') . ' 14:00');
                $fin = new \DateTime($jour->format('Y-m-d') . ' 18:00');
    
                while ($debut < $fin) {
                    $rdv = new RendezVous();
                    $rdv->setDateHeureRendezVous(clone $debut);
                    $rdv->setDescription('Créneau libre');
                    $rdv->setStatutRendezVous('disponible');
    
                    $em->persist($rdv);
                    $compteur++;
    
                    $debut->modify('+20 minutes');
                }
            }
    
            $jour->modify('+1 day');
        }
    
        $em->flush();
    
        if ($compteur > 0) {
            $this->addFlash('success', "$compteur créneaux de 20 minutes générés sur 1 mois !");
        } else {
            $this->addFlash('info', "Aucun créneau n'a été généré.");
        }
    
        return $this->redirectToRoute('admin');
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
        // 1) Vérifier la connexion
        $user = $this->getUser();
        if (!$user) {
            return new JsonResponse(['message' => 'Vous devez être connecté pour réserver.'], 403);
        }
    
        // 2) Récupérer les données JSON
        $data = json_decode($request->getContent(), true);
        if (!isset($data['id'])) {
            return new JsonResponse(['message' => 'ID du rendez-vous manquant.'], 400);
        }
    
        // 3) Trouver le rendez-vous
        $rendezVous = $rendezVousRepository->find($data['id']);
        if (!$rendezVous) {
            return new JsonResponse(['message' => 'Rendez-vous introuvable.'], 404);
        }
    
        // 4) Vérifier si le créneau est déjà réservé
        if ($rendezVous->getStatutRendezVous() === 'réservé') {
            return new JsonResponse(['message' => 'Ce créneau est déjà réservé.'], 400);
        }
    
        // 5) Réserver pour l'utilisateur
        $rendezVous->setStatutRendezVous('réservé');
        $rendezVous->setUtilisateur($user);
    
        $entityManager->persist($rendezVous);
        $entityManager->flush();
    
        // 6) Succès
        return new JsonResponse(['message' => 'Créneau réservé avec succès !'], 200);
    }
    
    

    public function annulerRendezVous(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $id = $data['id']; //  récupérer l'ID du rendez-vous à annuler

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

