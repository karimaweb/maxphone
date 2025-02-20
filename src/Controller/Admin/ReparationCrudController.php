<?php

namespace App\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use App\Entity\Reparation;
use App\Entity\Produit;
use App\Entity\Ticket;
use App\Entity\RendezVous;
use App\Entity\Utilisateur;

class ReparationCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Reparation::class;
    }

    /**
     * Vérification et sauvegarde d'une réparation
     */
    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof Reparation) {
            return;
        }

        $rendezVous = $entityInstance->getRendezVous();
        $client = $rendezVous ? $rendezVous->getUtilisateur() : $entityInstance->getUtilisateur();

        //  Vérifie si la réparation est associée à un client ou un rendez-vous
        if (!$rendezVous && !$client) {
            $this->addFlash('danger', 'Veuillez associer un client ou un rendez-vous à cette réparation.');
            return;
        }

        //  Création automatique d’un ticket si pas de rendez-vous
        if (!$rendezVous) {
            $ticket = new Ticket();
            $ticket->setObjetTicket("Réparation sans RDV");
            $ticket->setDescriptionTicket("Réparation ajoutée en magasin.");
            $ticket->setStatutTicket("En cours");
            $ticket->setDateCreationTicket(new \DateTime());
            $ticket->setUtilisateur($client);
            $ticket->setReparation($entityInstance);
            $entityManager->persist($ticket);
        }

        parent::persistEntity($entityManager, $entityInstance);
    }

    /**
     * Configuration des champs du CRUD
     */
    public function configureFields(string $pageName): iterable
    {
        return [
            // IdField::new('id')->hideOnForm(),
            TextField::new('diagnostic')->setLabel('Diagnostic'),
            DateTimeField::new('dateHeureReparation')->setLabel(' Date de Réparation'),

            // TextField::new('formattedStatut', 'Statut')
            // ->formatValue(fn ($value, $entity) => $entity->getFormattedStatut())
            // ->renderAsHtml(),
            //  Sélectionner un rendez-vous (optionnel)
            AssociationField::new('rendezVous', ' Rendez-vous')
                ->setRequired(false)
                ->formatValue(function ($value, $entity) {
                    return $value ? $value->getDateHeureRendezVous()->format('d/m/Y H:i') . ' - confirmé' : '<span class="badge badge-danger">Sans RDV</span>';
                })
                ->renderAsHtml()
                ->autocomplete(),
                // Permettre la modification du statut avec un ChoiceField
           //  Utiliser un champ "formaté" pour l'affichage dans l'index
        TextField::new('formattedStatut', 'Statut')
            ->formatValue(function ($value, $entity) {
                return $entity->getFormattedStatut();
            })
            ->renderAsHtml()
            ->onlyOnIndex(), //  Ce champ ne s'affiche que dans l'index

        //  Utiliser un `ChoiceField` pour l'édition
        ChoiceField::new('statutReparation', 'Statut')
            ->setChoices([
                'En attente' => 'en attente',
                'En cours' => 'en cours',
                'Terminé' => 'terminé',
            ])
            ->hideOnIndex(), //  Ce champ ne s'affiche que lors de l'édition

            //  Sélectionner un client si pas de rendez-vous
            AssociationField::new('utilisateur', ' Client')
                ->setRequired(false)
                ->formatValue(function ($value, $entity) {
                    return $value ? $value->getNomUtilisateur() . ' ' . $value->getPrenomUtilisateur() : '<span class="badge badge-danger">Aucun client</span>';
                })
                ->renderAsHtml()
                ->autocomplete(),

            //  Sélectionner un produit pour la réparation
            AssociationField::new('produit', ' Produit en réparation')
                ->setRequired(true)
                ->formatValue(function ($value, $entity) {
                    return $value ? $value->getLibelleProduit() : '<span class="badge badge-warning">Produit non défini</span>';
                })
                ->renderAsHtml()
                ->autocomplete(),

            //  Afficher les tickets associés à cette réparation
            AssociationField::new('tickets', ' Ticket associé')
                ->onlyOnDetail(),
        ];
    }

    /**
     * Mettre à jour l'état des tickets liés à la réparation
     */
    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
{
    if ($entityInstance instanceof Reparation) {
        foreach ($entityInstance->getTickets() as $ticket) {
            if (strtolower($entityInstance->getStatutReparation()) === 'terminé') {
                $ticket->setStatutTicket('Résolu'); //  On met bien à jour en "Résolu"
            } else {
                $ticket->setStatutTicket('En cours');
            }
            $entityManager->persist($ticket);
        }
        $entityManager->flush(); //  On enregistre les changements en base de données
    }

    parent::updateEntity($entityManager, $entityInstance);
}

}
