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

        // Vérifier si un produit est sélectionné
        $produit = $entityInstance->getProduit();
        if (!$produit) {
            $this->addFlash('danger', 'Veuillez sélectionner un produit pour la réparation.');
            return;
        }

        // Vérifier si la réparation est liée à un rendez-vous ou un client
        $rendezVous = $entityInstance->getRendezVous();
        $client = $rendezVous ? $rendezVous->getUtilisateur() : null;

        // Si aucun rendez-vous et aucun client, erreur
        if (!$rendezVous && !$client) {
            $this->addFlash('danger', 'Veuillez associer un client ou un rendez-vous à cette réparation.');
            return;
        }

        // Si pas de rendez-vous, créer un ticket pour cette réparation
        if (!$rendezVous) {
            $ticket = new Ticket();
            $ticket->setObjetTicket("Réparation sans RDV");
            $ticket->setDescriptionTicket("Réparation ajoutée directement en magasin.");
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
            IdField::new('id')->hideOnForm(),
            TextField::new('diagnostic')->setLabel('Diagnostic'),
            DateTimeField::new('dateHeureReparation')->setLabel('Date de Réparation'),

            ChoiceField::new('statutReparation')->setChoices([
                'En attente' => 'en attente',
                'En cours' => 'en cours',
                'Terminé' => 'terminé',
            ])->setLabel('Statut'),

            // Sélectionner un rendez-vous existant (si applicable)
            AssociationField::new('rendezVous', 'Rendez-vous (optionnel)')
                ->setRequired(false)
                ->setQueryBuilder(function ($qb) {
                    return $qb->andWhere('entity.statutRendezVous = :statut')
                              ->setParameter('statut', 'confirmé');
                })
                ->setCrudController(RendezVousCrudController::class) 
                ->autocomplete(),

            // Affichage du client sous forme de texte
            TextField::new('clientNom', 'Client')
                ->formatValue(function ($value, $entity) {
                    // Vérifier si la réparation est associée à un rendez-vous et un utilisateur
                    if ($entity->getRendezVous() && $entity->getRendezVous()->getUtilisateur()) {
                        return $entity->getRendezVous()->getUtilisateur()->getNomUtilisateur() . ' ' .
                               $entity->getRendezVous()->getUtilisateur()->getPrenomUtilisateur();
                    }

                    // Vérifier si la réparation est associée à un ticket et un utilisateur
                    if ($entity->getTickets() && $entity->getTickets()->count() > 0) {
                        $ticket = $entity->getTickets()->first();
                        if ($ticket && $ticket->getUtilisateur()) {
                            return $ticket->getUtilisateur()->getNomUtilisateur() . ' ' .
                                   $ticket->getUtilisateur()->getPrenomUtilisateur();
                        }
                    }

                    // Retourne un badge rouge si aucun client n'est trouvé
                    return '<span class="badge badge-danger">Aucun client</span>';
                })
                ->renderAsHtml() // Permet d'afficher le badge en HTML
                ->onlyOnIndex(), // Affiché uniquement dans la liste des réparations

            // Sélectionner un produit
            AssociationField::new('produit', 'Produit en réparation')
                ->setRequired(true)
                ->setQueryBuilder(function ($qb) {
                    return $qb->andWhere('p.typeProduit = :type')
                              ->setParameter('type', 'réparation');
                })
                ->autocomplete(),

            // Afficher les tickets associés à cette réparation
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
