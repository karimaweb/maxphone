<?php

namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use App\Entity\Image;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use Symfony\Component\Filesystem\Filesystem;

class ImageCrudController extends AbstractCrudController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public static function getEntityFqcn(): string
    {
        return Image::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setPageTitle('index', 'Liste des Images');
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            // IdField::new('id')->hideOnForm(),

            TextField::new('nomImage', 'Nom de l\'image')
                ->setHelp('Le nom de l\'image doit être unique et avoir une extension valide (jpg, png, gif).')
                ->setRequired(true),

            ImageField::new('nomImage', 'Image')
                ->setBasePath('images/')          // Chemin d'affichage des images
                ->setUploadDir('public/images/')  // Chemin d'upload
                ->setRequired(true)
                ->setHelp('Formats autorisés : JPG, PNG, GIF'),

            AssociationField::new('produit', 'Produit associé')
                ->setRequired(true)
                ->setHelp('Sélectionnez le produit auquel cette image est liée.'),
        ];
    }

    /**
     * Vérifie et ajoute une image
     */
    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof Image) {
            return;
        }

        // 1. Vérifier que le nom de l'image n'est pas vide et a une longueur minimale
        if (empty($entityInstance->getNomImage()) || strlen($entityInstance->getNomImage()) < 3) {
            $this->addFlash('danger', 'Le nom de l\'image doit contenir au moins 3 caractères.');
            return;
        }

        // 2. Vérifier l'extension du fichier
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        $extension = strtolower(pathinfo($entityInstance->getNomImage(), PATHINFO_EXTENSION));

        if (!in_array($extension, $allowedExtensions)) {
            $this->addFlash('danger', 'Extension de fichier non valide. Les formats autorisés sont : JPG, PNG et GIF.');
            return;
        }

        // 3. Vérifier si l'image existe déjà (unicité du nom)
        $existingImage = $entityManager->getRepository(Image::class)->findOneBy([
            'nomImage' => $entityInstance->getNomImage()
        ]);

        if ($existingImage) {
            $this->addFlash('danger', 'Une image avec ce nom existe déjà.');
            return;
        }

        // Enregistrement de la nouvelle image
        $entityManager->persist($entityInstance);
        $entityManager->flush();

        $this->addFlash('success', 'L\'image a été ajoutée avec succès.');
    }

    /**
     * Vérifie et met à jour une image
     */
    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof Image) {
            return;
        }

        // 1. Vérifier que le nom de l'image n'est pas vide et a une longueur minimale
        if (empty($entityInstance->getNomImage()) || strlen($entityInstance->getNomImage()) < 3) {
            $this->addFlash('danger', 'Le nom de l\'image doit contenir au moins 3 caractères.');
            return;
        }

        // 2. Vérifier l'extension du fichier
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        $extension = strtolower(pathinfo($entityInstance->getNomImage(), PATHINFO_EXTENSION));

        if (!in_array($extension, $allowedExtensions)) {
            $this->addFlash('danger', 'Extension de fichier non valide. Les formats autorisés sont : JPG, PNG et GIF.');
            return;
        }

        // 3. Vérifier l'unicité du nom pour les autres images
        $existingImage = $entityManager->getRepository(Image::class)->findOneBy([
            'nomImage' => $entityInstance->getNomImage()
        ]);

        if ($existingImage && $existingImage->getId() !== $entityInstance->getId()) {
            $this->addFlash('danger', 'Une autre image avec ce nom existe déjà.');
            return;
        }

        // Mise à jour de l'image
        $entityManager->flush();
        $this->addFlash('success', 'L\'image a été mise à jour avec succès.');
    }

    /**
     * Supprime une image et le fichier du serveur
     */
    public function deleteEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof Image) {
            return;
        }

        // Supprimer le fichier associé
        $filesystem = new Filesystem();
        $imagePath = 'public/images/' . $entityInstance->getNomImage();
        if ($filesystem->exists($imagePath)) {
            $filesystem->remove($imagePath);
        }

        $entityManager->remove($entityInstance);
        $entityManager->flush();

        $this->addFlash('success', 'L\'image a été supprimée avec succès.');
    }
}
