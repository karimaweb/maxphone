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

    public function findRdvByUserAndWeek($user, \DateTime $date, ?int $excludeId = null): ?RendezVous
    {
        $startOfWeek = (clone $date)->modify('monday this week')->setTime(0, 0);
        $endOfWeek = (clone $date)->modify('sunday this week')->setTime(23, 59, 59);
    
        $qb = $this->createQueryBuilder('r')
            ->andWhere('r.utilisateur = :user')
            ->andWhere('r.dateHeureRendezVous BETWEEN :start AND :end')
            ->setParameter('user', $user)
            ->setParameter('start', $startOfWeek)
            ->setParameter('end', $endOfWeek);
    
        if ($excludeId !== null) {
            $qb->andWhere('r.id != :excludeId')
               ->setParameter('excludeId', $excludeId);
        }
        dd($qb->getQuery()->getSQL(), $qb->getParameters());
        return $qb->getQuery()->getOneOrNullResult();
    }
    


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
