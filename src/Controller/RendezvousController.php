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
    EntityManagerInterface $entityManager,
    MailerInterface $mailer
): JsonResponse {
    $user = $this->getUser();
    if (!$user) {
        return new JsonResponse(['message' => 'Vous devez être connecté pour réserver.'], 403);
    }

    $data = json_decode($request->getContent(), true);
    if (!isset($data['id'])) {
        return new JsonResponse(['message' => 'ID du rendez-vous manquant.'], 400);
    }

    $rendezVous = $rendezVousRepository->find($data['id']);
    if (!$rendezVous) {
        return new JsonResponse(['message' => 'Rendez-vous introuvable.'], 404);
    }

    if ($rendezVous->getStatutRendezVous() === 'réservé') {
        return new JsonResponse(['message' => 'Ce créneau est déjà réservé.'], 400);
    }

    // ✅ Nouvelle vérification : l'utilisateur a-t-il déjà un RDV cette semaine ?
    $date = $rendezVous->getDateHeureRendezVous();
    $startOfWeek = (clone $date)->modify('Monday this week')->setTime(0, 0);
    $endOfWeek = (clone $date)->modify('Sunday this week')->setTime(23, 59);

    $rdvExistant = $rendezVousRepository->createQueryBuilder('r')
        ->andWhere('r.utilisateur = :user')
        ->andWhere('r.dateHeureRendezVous BETWEEN :start AND :end')
        ->andWhere('r.statutRendezVous = :statut')
        ->setParameter('user', $user)
        ->setParameter('start', $startOfWeek)
        ->setParameter('end', $endOfWeek)
        ->setParameter('statut', 'réservé')
        ->getQuery()
        ->getOneOrNullResult();

    if ($rdvExistant) {
        return new JsonResponse(['message' => 'Vous avez déjà un rendez-vous cette semaine.'], 400);
    }

    // Réserver
    $rendezVous->setStatutRendezVous('réservé');
    $rendezVous->setUtilisateur($user);
    $entityManager->persist($rendezVous);
    $entityManager->flush();

    // Envoi d'email
    $email = (new Email())
        ->from('noreply@maxphone.com')
        ->to($user->getEmail())
        ->subject('Confirmation de votre rendez-vous')
        ->text("Bonjour " . $user->getNomUtilisateur() . ",\nVotre rendez-vous est confirmé pour le " . $rendezVous->getDateHeureRendezVous()->format('d/m/Y H:i') . ".");
    $mailer->send($email);

    return new JsonResponse(['message' => 'Créneau réservé avec succès !']);
}

  

    #[Route('/annuler', name: 'rendezvous_annuler', methods: ['POST'])]
public function annuler(
    Request $request,
    RendezVousRepository $rendezVousRepository,
    EntityManagerInterface $em
): JsonResponse {
    $user = $this->getUser();

    if (!$user) {
        return new JsonResponse([
            'status' => 'error',
            'message' => 'Vous devez être connecté pour annuler un rendez-vous.'
        ], 403);
    }

    $data = json_decode($request->getContent(), true);

    if (!isset($data['id'])) {
        return new JsonResponse([
            'status' => 'error',
            'message' => 'ID du rendez-vous non fourni.'
        ], 400);
    }

    $rdv = $rendezVousRepository->find($data['id']);

    if (!$rdv) {
        return new JsonResponse([
            'status' => 'error',
            'message' => 'Rendez-vous introuvable.'
        ], 404);
    }

    //  Empêcher d'annuler les rendez-vous des autres
    if ($rdv->getUtilisateur() !== $user) {
        return new JsonResponse([
            'status' => 'error',
            'message' => 'Vous ne pouvez annuler que vos propres rendez-vous.'
        ], 400);
    }

    // Annuler le rendez-vous
    $rdv->setStatutRendezVous('disponible');
    $rdv->setUtilisateur(null); // dissocier l'utilisateur

    $em->persist($rdv);
    $em->flush();

    return new JsonResponse([
        'status' => 'success',
        'message' => 'Le rendez-vous a bien été annulé.'
    ]);
}

}

