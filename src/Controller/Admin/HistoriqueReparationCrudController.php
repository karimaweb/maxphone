<?php


namespace App\Controller\Admin;

use App\Entity\HistoriqueReparation;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;

class HistoriqueReparationCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return HistoriqueReparation::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            AssociationField::new('reparation', 'Réparation concernée'),
            TextField::new('statutHistoriqueReparation', 'Statut'),
            TextareaField::new('commentaire', 'Commentaire'),
            DateTimeField::new('dateMajReparation', 'Date de mise à jour'),
        ];
    }
}
