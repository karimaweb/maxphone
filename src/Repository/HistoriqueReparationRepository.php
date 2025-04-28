<?php

namespace App\Repository;

use App\Entity\HistoriqueReparation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<HistoriqueReparation>
 */
class HistoriqueReparationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HistoriqueReparation::class);
    }

    /**
     * Récupère l'historique d'une réparation spécifique, trié par date descendante
     */
    public function findLatestHistoriqueByReparation()
    {
        return $this->createQueryBuilder('h')
            ->select('h')
            ->where('h.dateMajReparation = (
                SELECT MAX(h2.dateMajReparation) 
                FROM App\Entity\HistoriqueReparation h2 
                WHERE h2.reparation = h.reparation
        )')
            ->orderBy('h.dateMajReparation', 'DESC')
            ->getQuery()
            ->getResult();
    }

    
    public function __toString(): string
    {
        return 'Historique Réparation'; // Remplace le nom par défaut
    }
     public function getHistoriqueClientsSimplifie(): array
    {
        $historiqueClients = [];

        foreach ($this->getUtilisateur()->getReparations() as $reparation) {
        $clientNom = $this->getUtilisateur()->getNomUtilisateur() . ' ' . $this->getUtilisateur()->getPrenomUtilisateur();

            if (!isset($historiqueClients[$clientNom])) {
                $historiqueClients[$clientNom] = [];
            }

        $dernierStatut = null;
        foreach ($reparation->getHistoriques() as $historique) {
            $statut = ucfirst($historique->getStatutHistoriqueReparation());

            // Ne garder que les changements majeurs en évitant la répétition
            if ($dernierStatut !== $statut) {
                $historiqueClients[$clientNom][] = [
                    'produit' => $reparation->getProduit(),
                    'date' => $historique->getDateMajReparation()->format('d/m/Y H:i'),
                    'statut' => $statut
                ];
                    $dernierStatut = $statut;
                }
            }
        }

        return $historiqueClients;
    }

}
