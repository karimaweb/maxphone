<?php

namespace App\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use App\Entity\RendezVous;
use App\Entity\Utilisateur;

class RendezVousCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return RendezVous::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            DateTimeField::new('dateHeureRendezVous')->setLabel('Date et Heure'),
            
            ChoiceField::new('statutRendezVous')->setChoices([
                'En attente' => 'en attente',
                'Confirmé' => 'confirmé',
                'Annulé' => 'annulé',
            ])->setLabel('Statut'),

            // Champ Description
            TextField::new('description')->setLabel('Description')
                ->setHelp('Ajoutez une description pour ce rendez-vous')
                ->setRequired(true),

            // Associer un client
            AssociationField::new('utilisateur', 'Client')
                ->setRequired(true)
                ->autocomplete(),

            
        // ✅ Ajout des réparations associées
        AssociationField::new('reparations', 'Réparations associées')
        ->setFormTypeOptions(['by_reference' => false])
        ->setRequired(false)
        ->hideOnIndex(),
];
        
    }
}

