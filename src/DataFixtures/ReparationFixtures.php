<?php

namespace App\DataFixtures;

use App\Entity\Reparation;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ReparationFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i <= 5; $i++) {
            $reparation = new Reparation();
            $reparation->setDiagnostic('Diagnostic ' . $i)
                ->setDateHeureReparation(new \DateTime())
                ->setStatutReparation($i % 2 === 0 ? 'En cours' : 'Terminé');

            // Si vous utilisez des relations, associez un produit ou un utilisateur
            $reparation->setProduit($this->getReference('produit_' . $i));
            
            $manager->persist($reparation);

            // Ajoutez une référence pour d'autres entités qui en dépendent
            $this->addReference('reparation_' . $i, $reparation);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            ProduitFixtures::class, // Charger ProduitFixtures avant cette fixture
        ];
    }
}
