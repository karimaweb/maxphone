<?php

namespace App\Repository;

use App\Entity\Produit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Produit>
 */
class ProduitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Produit::class);
    }

    //    /**
    //     * @return Produit[] Returns an array of Produit objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Produit
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
    public function findProduitsEnVente(): array
{
    return $this->createQueryBuilder('p')
        ->where('p.typeProduit = :type')
        ->setParameter('type', 'vente')
        ->getQuery()
        ->getResult();
}
public function findProduitsEnReparation()
{
    return $this->createQueryBuilder('p')
        ->where('p.typeProduit = :type')
        ->setParameter('type', 'réparation')
        ->getQuery()
        ->getResult();
}
// src/Repository/ProduitRepository.php

// src/Repository/ProduitRepository.php

public function findProduitsReparation(): array
{
    return $this->createQueryBuilder('p')
        ->where('p.typeProduit = :type')
        ->setParameter('type', 'réparation')
        ->getQuery()
        ->getResult();
}


}
