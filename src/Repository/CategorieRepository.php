<?php

namespace App\Repository;

use App\Entity\Categorie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Categorie>
 */
class CategorieRepository extends ServiceEntityRepository

{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Categorie::class);
    }

    
    public function findParentCategories()
    {
        return $this->createQueryBuilder('c')
            ->where('c.parent IS NULL')
            ->getQuery()
            ->getResult();
    }
    //activer la barre de recherche
    public function findBySearchTerm(string $term): array
    {
        return $this->createQueryBuilder('c')
        ->where('c.nomCategorie LIKE :term')
        ->setParameter('term', '%'.$term.'%')
        ->getQuery()
        ->getResult();
    }

}
