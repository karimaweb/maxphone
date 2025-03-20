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
        $joursAutorises = ['Wednesday', 'Friday']; // 🔹 Seuls Mercredi et Vendredi
    
        $now = new \DateTime(); // 🔹 Date actuelle
        $dateFin = (clone $now)->modify('last day of +1 months'); //  Génération jusqu'à la fin du mois suivant
    
        $compteur = 0;
    
        while ($now <= $dateFin) {
            $jourSemaine = $now->format('l'); //  Format complet du jour (Wednesday, Friday, etc.)
    
            if (in_array($jourSemaine, $joursAutorises, true)) {
                // 🔹 Créneaux de 14:00 à 18:00 par intervalles de 20 minutes
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
            'statut' => $statut, //  Ajout du statut
        ];
    }
    
    return new JsonResponse($events);
}
#[Route('/reserver', name: 'rendezvous_reserver', methods: ['POST'])]
public function reserver(Request $request, RendezVousRepository $rendezVousRepository, EntityManagerInterface $entityManager): JsonResponse
{
    //  Vérifie si l'utilisateur est connecté
    if (!$this->getUser()) {
        return new JsonResponse(['message' => 'Vous devez être connecté pour réserver un créneau.'], 403);
    }

    $data = json_decode($request->getContent(), true);
    $rendezVous = $rendezVousRepository->find($data['id']);

    if (!$rendezVous) {
        return new JsonResponse(['message' => 'Créneau introuvable'], 404);
    }

    // Vérifie si le créneau est déjà réservé
    if ($rendezVous->getStatutRendezVous() === 'réservé') {
        return new JsonResponse(['message' => 'Ce créneau est déjà réservé.'], 400);
    }

    //  Associe le créneau à l'utilisateur connecté
    $rendezVous->setStatutRendezVous('réservé');
    $rendezVous->setUtilisateur($this->getUser());

    $entityManager->persist($rendezVous);
    $entityManager->flush();

    return new JsonResponse(['message' => 'Créneau réservé avec succès !']);
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

    


    
}

