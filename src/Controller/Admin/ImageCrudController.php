<?php

namespace App\Controller\Admin;

use App\Entity\Image;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;

class ImageCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Image::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('nomImage', 'Nom de l\'image'),
            ImageField::new('nomImage', 'Image')
                ->setBasePath('/images/')
                ->setUploadDir('public/images/')
                ->setUploadedFileNamePattern('[randomhash].[extension]')
                ->setRequired(false),
            AssociationField::new('produit', 'Produit associé'),
        ];
    }
}
