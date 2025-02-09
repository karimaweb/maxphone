<?php

namespace App\DataFixtures;

use App\Entity\Ticket;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class TicketFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i <= 10; $i++) {
            $ticket = new Ticket();
            $ticket->setObjetTicket('Ticket ' . $i)
                ->setDescriptionTicket('Problème ' . $i)
                ->setStatutTicket($i % 2 === 0 ? 'Ouvert' : 'Fermé')
                ->setDateCreationTicket(new \DateTime())
                ->setReparation($this->getReference('reparation_' . ($i % 5 + 1))) // Référence à une réparation existante
                ->setUtilisateur($this->getReference('utilisateur_' . ($i % 3 + 1))); // Référence à un utilisateur

            $manager->persist($ticket);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UtilisateurFixtures::class,
            ReparationFixtures::class,
        ];
    }
}
