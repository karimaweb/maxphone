<?php

namespace App\Controller;

use App\Entity\Ticket;
use App\Form\TicketType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TicketController extends AbstractController
{
    #[Route('/ticket/create', name: 'ticket_create')]
    public function createTicket(Request $request, EntityManagerInterface $em): Response
    {
        $ticket = new Ticket();
        $form = $this->createForm(TicketType::class, $ticket);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $ticket->setUtilisateur($this->getUser());
            $ticket->setStatutTicket('en attente');
            $em->persist($ticket);
            $em->flush();

            return $this->redirectToRoute('ticket_list');
        }

        return $this->render('ticket/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
