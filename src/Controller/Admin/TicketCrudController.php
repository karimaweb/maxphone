<?php

namespace App\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use App\Entity\Ticket;
use App\Entity\Utilisateur;

class TicketCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Ticket::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            // IdField::new('id')->hideOnForm(),
            TextField::new('objetTicket')->setLabel('Objet'),
            TextField::new('descriptionTicket')->setLabel('Description'),
            ChoiceField::new('statutTicket')->setChoices([
                'En cours' => 'en cours',
                'Résolu' => 'résolu',
            ])->setLabel('Statut'),

            DateTimeField::new('dateCreationTicket')->setLabel('Date de création'),

            // Associer un client si existant
            AssociationField::new('utilisateur', 'Client')
                ->setRequired(false)
                ->autocomplete(),

            // Lier à la réparation associée
            AssociationField::new('reparation', 'Réparation associée')
                ->setRequired(false)
                ->autocomplete(),
        ];
    }
}
