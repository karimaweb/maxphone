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

class TicketCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Ticket::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('objetTicket', 'Objet du ticket'),
            TextareaField::new('descriptionTicket', 'Description'),
            TextField::new('statutTicket', 'Statut'),
            DateTimeField::new('dateCreationTicket', 'Date de création'),
            AssociationField::new('reparation', 'Réparation associée')->setRequired(false),
            AssociationField::new('utilisateur', 'Le client associé')->formatValue(function ($value, $entity) {
                return $entity->getUtilisateur() ? 
                    $entity->getUtilisateur()->getNomUtilisateur() . ' ' . 
                    $entity->getUtilisateur()->getPrenomUtilisateur() : 'Non assigné';
            }),
        ];
    }
}
