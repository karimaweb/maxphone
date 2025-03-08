<?php

namespace App\Controller\Admin;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use App\Entity\Ticket;

class TicketCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Ticket::class;
    }

        public function configureFields(string $pageName): iterable
    {
        $fields = [];

        // Champ "Objet" du ticket
        $fields[] = TextField::new('objetTicket')->setLabel('Objet');

        //  Afficher le statut sous forme de badge dans la liste des tickets (Index)
        if ($pageName === 'index') {
            $fields[] = TextField::new('formattedStatut', 'Statut')
                ->formatValue(fn ($value, $entity) => $entity->getFormattedStatut())
                ->renderAsHtml();
        }

        //  Ajouter un menu déroulant dans le formulaire (Edit & New)
        if ($pageName === 'edit' || $pageName === 'new') {
            $fields[] = ChoiceField::new('statutTicket', 'Statut')
                ->setChoices([
                    'Ouvert' => 'ouvert',
                    'En cours' => 'encours',
                    'Résolu' => 'resolu',
                    'Fermé' => 'ferme',
                ])
                ->setRequired(true) // Rendre ce champ obligatoire
                // ->renderExpanded(false) // Permet d'afficher un menu déroulant
                ->setValue(fn ($entity) => $entity?->getStatutTicket()); // Récupère la valeur actuelle du statut
        }

        // Champ "Date de création" (non modifiable)
        $fields[] = DateTimeField::new('dateCreationTicket')->setLabel('Date de création')
            ->setDisabled(true); // Empêcher la modification de la date 

        // Champ "Client" (non modifiable)
        $fields[] = AssociationField::new('utilisateur', 'Client')
            ->setRequired(false)
            ->setDisabled(true) //  L'utilisateur ne peut pas être changé
            ->autocomplete();

        // Champ "Réparation associée"
        $fields[] = AssociationField::new('reparation', 'Réparation associée')
            ->setRequired(false)
            ->autocomplete();

        return $fields;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->disable(Action::NEW); // je désactive le bouton "Add Ticket" admin n'a pas besoin de créer un ticket
    }
}
