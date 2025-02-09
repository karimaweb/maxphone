<?php

namespace App\DataFixtures;

use App\Entity\Image;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class ImageFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i <= 10; $i++) {
            $image = new Image();
            $image->setNomImage('image_' . $i . '.png')
                ->setProduit($this->getReference('produit_' . ($i % 5 + 1))); // Associe à un produit

            $manager->persist($image);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            ProduitFixtures::class, // Charge d'abord les produits
        ];
    }
}
