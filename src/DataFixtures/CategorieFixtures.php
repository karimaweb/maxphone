<?php

namespace App\DataFixtures;

use App\Entity\Categorie;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategorieFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Liste des catégories principales
        $categories = [
            'Smartphones',
            'Tablettes',
            'Accessoires',
            'Ordinateurs portables',
            'Écrans',
            'Imprimantes',
        ];

        foreach ($categories as $index => $nomCategorie) {
            $categorie = new Categorie();
            $categorie->setNomCategorie($nomCategorie);

            $manager->persist($categorie);

            // Ajout d'une référence pour utiliser cette catégorie dans d'autres fixtures
            $this->addReference('categorie_' . $index, $categorie);
        }

        $manager->flush();
    }
}
