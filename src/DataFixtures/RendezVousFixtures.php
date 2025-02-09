<?php

namespace App\DataFixtures;

use App\Entity\RendezVous;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class RendezVousFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i <= 5; $i++) {
            $rendezVous = new RendezVous();
            $rendezVous->setDateHeureRendezVous(new \DateTime('+ ' . $i . ' days'))
                ->setStatutRendezVous('Confirmé')
                ->setDescription('Rendez-vous ' . $i);

            $manager->persist($rendezVous);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            ReparationFixtures::class, // Lié aux réparations
        ];
    }
}
