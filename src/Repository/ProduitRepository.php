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

    public function findProduitsEnVente(): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.typeProduit = :type')
            ->setParameter('type', 'vente')
            ->getQuery()
            ->getResult();
    }

    // src/Repository/ProduitRepository.php

    public function findProduitsReparation(): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.typeProduit = :type')
            ->setParameter('type', 'réparation')
            ->getQuery()
            ->getResult();
    }
    // Activer la barre de rechrche
    public function findBySearchTerm(string $term): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.libelleProduit LIKE :term')
            ->setParameter('term', '%'.$term.'%')
            ->getQuery()
            ->getResult();
    }

}
