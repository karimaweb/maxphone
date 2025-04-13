<?php

namespace App\Repository;

use App\Entity\Reparation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Reparation>
 */
class ReparationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reparation::class);
    }
     // Méthode pour récupérer les réparations en cours (par exemple, statut non terminé)
    public function findEnCours($user): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.user = :user')
            ->andWhere('r.statut != :terminé')
            ->setParameter('user', $user)
            ->setParameter('terminé', 'Terminé')
            ->getQuery()
            ->getResult();
    }
    
    // Méthode pour récupérer l'historique complet (toutes réparations, ou selon vos critères)
    public function findHistorique($user): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.user = :user')
            ->setParameter('user', $user)
            ->orderBy('r.dateDepot', 'DESC')
            ->getQuery()
            ->getResult();
    }
    //    /**
    //     * @return Reparation[] Returns an array of Reparation objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('r.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Reparation
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
