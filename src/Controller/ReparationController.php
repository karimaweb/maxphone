<?php
namespace App\Controller;

use App\Entity\HistoriqueReparation;
use App\Repository\ReparationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Reparation;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

#[Route('/reparation')]
class ReparationController extends AbstractController
{
    #[Route('/reparations', name: 'reparation_index')]
    public function index(): Response
    {
        return $this->render('reparations/index.html.twig');
    }

    #[Route('/update/{id}', name: 'reparation_update')]
    #[Route('/update/{id}', name: 'reparation_update')]
    public function updateReparationStatus(EntityManagerInterface $entityManager, MailerInterface $mailer, Reparation $reparation): Response
    {
        $utilisateur = $reparation->getUtilisateur();
        if (!$utilisateur) {
            return new Response(" Aucun utilisateur associé à cette réparation.", 400);
        }
    
        $utilisateurEmail = $utilisateur->getEmail();
        dump("🔹 Email à envoyer à : " . $utilisateurEmail); // Vérifie que l'email est bien récupéré
    
        if ($reparation->getStatutReparation() === 'terminé') {
            
            $this->sendRepairCompletionEmail($mailer, $utilisateurEmail);
           
        } 
        $entityManager->flush();
        die(); // Arrête l'exécution pour voir les `dump()`
    
        return new Response(' Statut mis à jour et email envoyé si la réparation est terminée.');
    }
    

    private function sendRepairCompletionEmail(MailerInterface $mailer, string $utilisateurEmail)
{
    try {
      
        $email = (new Email())
            ->from('noreply@maxphone.com')
            ->to($utilisateurEmail)
            ->subject('Votre réparation est terminée')
            ->text("Votre réparation est terminée. Vous pouvez récupérer votre appareil.")
            ->html("<p>Votre réparation est terminée. Vous pouvez récupérer votre appareil.</p>");

        $mailer->send($email);

        
        die(); // Pour voir le dump directement
    } catch (\Symfony\Component\Mailer\Exception\TransportExceptionInterface $e) {
    
    }
}

    
}


