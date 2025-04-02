<?php

namespace App\Controller;

use App\Entity\Ticket;
use App\Form\TicketType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\MailerInterface;

class TicketController extends AbstractController
{
    #[Route('/ticket/new', name: 'ticket_create', methods: ['GET','POST'])]
    public function new(Request $request, EntityManagerInterface $em, MailerInterface $mailer): Response
    {
     
        $ticket = new Ticket();
        $form = $this->createForm(TicketType::class, $ticket, [
            'user' => $this->getUser(),
        ]);
        $form->handleRequest($request);

        // Variable pour stocker le message de succès
        $successMessage = null;

        if ($form->isSubmitted() && $form->isValid()) {
            // 1) Envoyer l'e-mail (ou toute autre logique)
            $ticket->setStatutTicket('en attente');
            $ticket->setDateCreationTicket(new \DateTime());
            $ticket->setUtilisateur($this->getUser());

            $email = (new Email())
                ->from($this->getUser()->getEmail())
                ->to('admin@votre-domaine.com')
                ->subject('Nouveau Ticket - ' . $ticket->getObjetTicket())
                ->text(
                    "Nouveau ticket de : "
                    . $this->getUser()->getNomUtilisateur() . " " . $this->getUser()->getPrenomUtilisateur() . "\n"
                    . "Objet : " . $ticket->getObjetTicket() . "\n"
                    . "Message : " . $ticket->getDescriptionTicket()
                );
            $mailer->send($email);

            // 2) Définir un message de succès
            $successMessage = "Votre réclamation a été envoyée avec succés.";

            // 3) Créer un NOUVEL objet Ticket et un NOUVEAU formulaire VIERGE
            $ticket = new Ticket();
            $form = $this->createForm(TicketType::class, $ticket, [
                'user' => $this->getUser(),
            ]);
            //je laisse le formulaire vide 
        }

        // 4) Renvoyer la même vue, avec le formulaire  et le message
        return $this->render('ticket/new.html.twig', [
            'form' => $form->createView(),
            'successMessage' => $successMessage,
        ]);
    }
}
