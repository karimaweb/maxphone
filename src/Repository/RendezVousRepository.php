<?php

namespace App\Repository;

use App\Entity\RendezVous;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RendezVous>
 */
class RendezVousRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RendezVous::class);
    }

    //    /**
    //     * @return RendezVous[] Returns an array of RendezVous objects
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

    //    public function findOneBySomeField($value): ?RendezVous
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
    public function genererCreneaux($mois, $annee)
{
    $jours = ['Wednesday', 'Friday']; // Mercredi et Vendredi
    $heures = ['14:00', '15:00', '16:00', '17:00'];

    foreach ($jours as $jour) {
        for ($i = 1; $i <= 31; $i++) {
            $dateStr = "$annee-$mois-$i $jour";
            $date = new \DateTime($dateStr);

            if ($date->format('N') == 3 || $date->format('N') == 5) { // Mercredi = 3, Vendredi = 5
                foreach ($heures as $heure) {
                    $dateCreneau = new \DateTime($date->format('Y-m-d') . " " . $heure);

                    $creneau = new RendezVous();
                    $creneau->setDateHeureRendezVous($dateCreneau);
                    $creneau->setStatutRendezVous('disponible');

                    $this->_em->persist($creneau);
                }
            }
        }
    }

    $this->_em->flush();
}
public function findAvailableCreneaux()
{
    return $this->createQueryBuilder('r')
        ->where('r.dateHeureRendezVous >= :now')
        ->setParameter('now', new \DateTime())
        ->getQuery()
        ->getResult();
}

}
