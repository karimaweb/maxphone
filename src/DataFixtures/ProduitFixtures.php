<?php

namespace App\DataFixtures;

use App\Entity\Produit;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class ProduitFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $produits = [
            ['libelle' => 'iPhone 14', 'prix' => 1099.99, 'type' => 'vente', 'stock' => 10, 'categorie' => 'categorie_0'],
            ['libelle' => 'Samsung Galaxy S23', 'prix' => 999.99, 'type' => 'vente', 'stock' => 15, 'categorie' => 'categorie_0'],
            ['libelle' => 'Chargeur USB-C', 'prix' => 29.99, 'type' => 'les deux', 'stock' => 50, 'categorie' => 'categorie_1'],
            ['libelle' => 'Écran LCD iPhone', 'prix' => 89.99, 'type' => 'réparation', 'stock' => 5, 'categorie' => 'categorie_2'],
            ['libelle' => 'Batterie Samsung', 'prix' => 49.99, 'type' => 'réparation', 'stock' => 20, 'categorie' => 'categorie_2'],
        ];

        foreach ($produits as $data) {
            $produit = new Produit();
            $produit->setLibelleProduit($data['libelle'])
                ->setPrixUnitaire($data['prix'])
                ->setTypeProduit($data['type'])
                ->setQteStock($data['stock'])
                ->setCategorie($this->getReference($data['categorie'])); // Utilisation correcte

            $manager->persist($produit);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            CategorieFixtures::class, // Dépendance à `CategorieFixtures`
        ];
    }
}
