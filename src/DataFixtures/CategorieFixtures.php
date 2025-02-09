<?php

namespace App\DataFixtures;

use App\Entity\Categorie;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategorieFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $categories = ['Téléphones', 'Accessoires', 'Écrans'];

        foreach ($categories as $index => $nom) {
            $categorie = new Categorie();
            $categorie->setNomCategorie($nom);
            $manager->persist($categorie);

            // Ajouter une référence unique
            $this->addReference('categorie_' . $index, $categorie);
        }

        $manager->flush();
    }
}
