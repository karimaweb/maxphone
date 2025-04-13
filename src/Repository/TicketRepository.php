<?php

namespace App\Repository;

use App\Entity\Ticket;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Ticket>
 */
class TicketRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ticket::class);
    }
    public function updateTicketStatus(Reparation $reparation)
    {
        $tickets = $this->findBy(['reparation' => $reparation]);
    
        foreach ($tickets as $ticket) {
            if ($reparation->getStatutReparation() === 'Terminé') {
                $ticket->setStatutTicket('résolu');
            } else {
                $ticket->setStatutTicket('en cours');
            }
            $this->_em->persist($ticket);
        }
        $this->_em->flush();
    }
    
}
