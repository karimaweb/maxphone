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

    //  On autorise les réparations sans rendez-vous si un client est sélectionné
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
            DateTimeField::new('dateHeureReparation')->setLabel('Date de Réparation'),

            ChoiceField::new('statutReparation')->setChoices([
                'En attente' => 'en attente',
                'En cours' => 'en cours',
                'Terminé' => 'terminé',
            ])->setLabel('Statut'),

            //  Sélectionner un rendez-vous (optionnel)
            AssociationField::new('rendezVous', 'Rendez-vous (optionnel)')
                ->setRequired(false)
                ->setQueryBuilder(function ($qb) {
                    return $qb->andWhere('entity.statutRendezVous = :statut')
                              ->setParameter('statut', 'confirmé');
                })
                ->setCrudController(RendezVousCrudController::class)
                ->autocomplete(),

            //  Sélectionner un client si pas de rendez-vous
            AssociationField::new('utilisateur', 'Client')
                ->setRequired(false)
                ->setCrudController(UtilisateurCrudController::class)
                ->autocomplete(),

            //  Sélectionner un produit pour la réparation
            AssociationField::new('produit', 'Produit en réparation')
                ->setRequired(true)
                ->setQueryBuilder(function ($qb) {
                    return $qb->andWhere('p.typeProduit = :type')
                              ->setParameter('type', 'réparation');
                })
                ->autocomplete(),

            //  Afficher les tickets associés à cette réparation
            AssociationField::new('tickets', 'Ticket associé')
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
                if ($entityInstance->getStatutReparation() === 'Terminé') {
                    $ticket->setStatutTicket('Résolu');
                } else {
                    $ticket->setStatutTicket('En cours');
                }
                $entityManager->persist($ticket);
            }
            $entityManager->flush();
        }

        parent::updateEntity($entityManager, $entityInstance);
    }
}
