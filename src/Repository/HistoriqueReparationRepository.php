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
    public function findHistoriqueByReparation($reparationId)
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.reparation = :reparationId')
            ->setParameter('reparationId', $reparationId)
            ->orderBy('h.dateMiseAJour', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
