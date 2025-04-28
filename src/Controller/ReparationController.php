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
    public function updateReparationStatus(EntityManagerInterface $entityManager, MailerInterface $mailer, Reparation $reparation): Response
    {
        $utilisateur = $reparation->getUtilisateur();
        if (!$utilisateur) {
            return new Response(" Aucun utilisateur associé à cette réparation.", 400);
        }
    
        $utilisateurEmail = $utilisateur->getEmail();
        
    
        if ($reparation->getStatutReparation() === 'terminé') {
            
            $this->sendRepairCompletionEmail($mailer, $utilisateurEmail);
           
        } 
        $entityManager->flush();
        die(); // Arrête l'exécution pour voir les `dump()`
    
        return new Response(' Statut mis à jour et email envoyé si la réparation est terminée.');
    }
    

   
    
    }


    



