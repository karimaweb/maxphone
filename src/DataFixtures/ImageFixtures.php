<?php

namespace App\DataFixtures;

use App\Entity\Image;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Symfony\Component\Filesystem\Filesystem;

class ImageFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        // Chemin vers le dossier des images
        $imageDirectory = __DIR__ . 'public/images';

        // Vérifier que le dossier existe
        $filesystem = new Filesystem();
        if (!$filesystem->exists($imageDirectory)) {
            throw new \Exception("Le dossier des images ($imageDirectory) est introuvable.");
        }

        // Récupérer la liste des fichiers d'images
        $imageFiles = array_diff(scandir($imageDirectory), ['.', '..']);

        $index = 0;

        foreach ($imageFiles as $imageFile) {
            $image = new Image();
            $image->setNomImage($imageFile); // Enregistrez simplement le nom du fichier

            // Associer chaque image à un produit
            $produitReference = 'produit_' . ($index % 5); // Associer cycliquement aux produits
            $image->setProduit($this->getReference($produitReference));

            $manager->persist($image);
            $index++;
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            ProduitFixtures::class, // Dépend de ProduitFixtures
        ];
    }
}
