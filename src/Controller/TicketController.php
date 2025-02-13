<?php

namespace App\Controller;
use App\Entity\Ticket;
use App\Repository\TicketRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\TicketType;

#[Route('/aide', name: 'aide_')]
class TicketController extends AbstractController
{
   
    #[Route('/', name: 'index')]
#[Route('/tickets', name: 'tickets')]
    public function tickets(Request $request, EntityManagerInterface $entityManager): Response
    {
        $ticket = new Ticket();
        $form = $this->createForm(TicketType::class, $ticket);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $ticket->setDateSoumission(new \DateTime()); // Ajoute la date actuelle

            $entityManager->persist($ticket);
            $entityManager->flush();

            $this->addFlash('success', 'Votre ticket a été envoyé avec succès.');
            return $this->redirectToRoute('aide_tickets');
        }

        return $this->render('aide/tickets.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}