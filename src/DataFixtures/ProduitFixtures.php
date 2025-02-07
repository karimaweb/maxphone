<?php

namespace App\DataFixtures;

use App\Entity\Produit;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProduitFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $produits = [
            ['libelle' => 'iPhone 14', 'prix' => 1099.99, 'type' => 'vente', 'stock' => 10],
            ['libelle' => 'Samsung Galaxy S23', 'prix' => 999.99, 'type' => 'vente', 'stock' => 15],
            ['libelle' => 'Chargeur USB-C', 'prix' => 29.99, 'type' => 'accessoire', 'stock' => 50],
            ['libelle' => 'Écran LCD iPhone', 'prix' => 89.99, 'type' => 'réparation', 'stock' => 5],
            ['libelle' => 'Batterie Samsung', 'prix' => 49.99, 'type' => 'réparation', 'stock' => 20],
        ];

        foreach ($produits as $index => $data) {
            $produit = new Produit();
            $produit->setLibelleProduit($data['libelle'])
                ->setPrixUnitaire($data['prix'])
                ->setTypeProduit($data['type'])
                ->setQteStock($data['stock']);

            $manager->persist($produit);

            // Ajoutez une référence pour chaque produit
            $this->addReference('produit_' . $index, $produit);
        }

        $manager->flush();
    }
}
