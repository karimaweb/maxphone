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
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
                ->setBasePath('images/') // Chemin d'affichage des images
                ->setUploadDir('public/images/') // Chemin d'upload
                ->setRequired(true)
                ->setHelp('Formats autorisés : JPG, PNG, GIF'),

            AssociationField::new('produit', 'Produit associé')
                ->setRequired(true)
                ->setHelp('Sélectionnez le produit auquel cette image est liée.'),
        ];
    }

    //  Vérifier et ajouter une image
    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof Image) {
            return;
        }

        // Vérifier si l'image existe déjà
        $existingImage = $entityManager->getRepository(Image::class)->findOneBy([
            'nomImage' => $entityInstance->getNomImage()
        ]);

        if ($existingImage) {
            $this->addFlash('danger', 'Une image avec ce nom existe déjà.');
            return;
        }

        $entityManager->persist($entityInstance);
        $entityManager->flush();

        $this->addFlash('success', 'L\'image a été ajoutée avec succès.');
    }

    //  Vérifier et mettre à jour une image
    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof Image) {
            return;
        }

        $existingImage = $entityManager->getRepository(Image::class)->findOneBy([
            'nomImage' => $entityInstance->getNomImage()
        ]);

        if ($existingImage && $existingImage->getId() !== $entityInstance->getId()) {
            $this->addFlash('danger', 'Une autre image avec ce nom existe déjà.');
            return;
        }

        $entityManager->flush();
        $this->addFlash('success', 'L\'image a été mise à jour avec succès.');
    }

    //  Supprimer une image et le fichier du serveur
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
