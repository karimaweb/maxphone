<?php

namespace App\Controller\Admin;

use App\Entity\Ticket;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;


class TicketCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Ticket::class;
    }

    public function configureFields(string $pageName): iterable
{
    return [
        IdField::new('id')->hideOnForm(),
        TextField::new('objetTicket', 'Objet'),
        TextareaField::new('descriptionTicket', 'Description'),
        ChoiceField::new('statutTicket', 'Statut')->setChoices([
            'En attente' => 'en attente',
            'En cours' => 'en cours',
            'Résolu' => 'résolu',
        ]),
        AssociationField::new('utilisateur', 'Client')->setDisabled(), // Affichage de l'utilisateur
        AssociationField::new('reparation', 'Réparation associée')
            ->setRequired(false)
            ->autocomplete(),
    ];
}
}