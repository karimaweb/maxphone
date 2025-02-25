<?php

namespace App\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use App\Entity\Reparation;
use App\Entity\Produit;
use App\Entity\Ticket;
use App\Entity\RendezVous;
use App\Entity\Utilisateur;

class ReparationCrudController extends AbstractCrudController
{
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public static function getEntityFqcn(): string
    {
        return Reparation::class;
    }

    /**
     * Vérification et sauvegarde d'une réparation avec messages flash
     */
    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof Reparation) {
            return;
        }

        $flashBag = $this->requestStack->getSession()->getFlashBag();

        // ✅ Vérification du diagnostic
        if (strlen($entityInstance->getDiagnostic()) < 5) {
            $flashBag->add('danger', 'Le diagnostic doit contenir au moins 5 caractères.');
            return;
        }

        // ✅ Vérification de la date (ne peut pas être dans le passé)
        $now = new \DateTime();
        if ($entityInstance->getDateHeureReparation() < $now) {
            $flashBag->add('danger', 'La date de réparation ne peut pas être dans le passé.');
            return;
        }

        $rendezVous = $entityInstance->getRendezVous();
        $client = $rendezVous ? $rendezVous->getUtilisateur() : $entityInstance->getUtilisateur();

        // ✅ Vérification qu'un client est bien associé
        if (!$rendezVous && !$client) {
            $flashBag->add('danger', 'Veuillez associer un client ou un rendez-vous à cette réparation.');
            return;
        }

        // ✅ Création automatique d’un ticket si pas de rendez-vous
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
        $flashBag->add('success', 'Réparation ajoutée avec succès.');
    }

    /**
     * Configuration des champs du CRUD
     */
    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('diagnostic')->setLabel('Diagnostic')
                ->setRequired(true)
                ->setHelp('Minimum 5 caractères'),

            DateTimeField::new('dateHeureReparation')->setLabel(' Date de Réparation')
                ->setRequired(true)
                ->setHelp('Ne peut pas être dans le passé'),

            AssociationField::new('rendezVous', ' Rendez-vous')
                ->setRequired(false)
                ->formatValue(function ($value, $entity) {
                    return $value ? $value->getDateHeureRendezVous()->format('d/m/Y H:i') : '<span class="badge badge-danger">Sans RDV</span>';
                })
                ->renderAsHtml()
                ->autocomplete(),

            ChoiceField::new('statutReparation', 'Statut')
                ->setChoices([
                    'En attente' => 'en attente',
                    'En cours' => 'en cours',
                    'Terminé' => 'terminé',
                ])
                ->setRequired(true)
                
                ->formatValue(function ($value, $entity) {
                    if ($value === 'en attente') {
                        return '<span class="badge badge-warning">En attente</span>';
                    } elseif ($value === 'en cours') {
                        return '<span class="badge badge-info">En cours</span>';
                    } elseif ($value === 'terminé') {
                        return '<span class="badge badge-success">Terminé</span>';
                    }
                    return $value;
                }),

            AssociationField::new('utilisateur', 'Client')
                ->setRequired(false)
                ->formatValue(function ($value, $entity) {
                    return $value ? $value->getNomUtilisateur() . ' ' . $value->getPrenomUtilisateur() : '<span class="badge badge-danger">Aucun client</span>';
                })
                ->renderAsHtml()
                ->setHelp('<a href="/admin?crudAction=new&crudControllerFqcn=App\Controller\Admin\UtilisateurCrudController" class="btn btn-primary" target="_blank" style="margin-top:5px;">Ajouter un client</a>')
                ->autocomplete(),

            AssociationField::new('produit', ' Produit en réparation')
                ->setRequired(true)
                ->formatValue(function ($value, $entity) {
                    return $value ? $value->getLibelleProduit() : '<span class="badge badge-warning">Produit non défini</span>';
                })
                ->renderAsHtml()
                ->autocomplete()
                ->setHelp('<a href="/admin?crudAction=new&crudControllerFqcn=App\Controller\Admin\ProduitCrudController" class="btn btn-primary" target="_blank" style="margin-top:5px;">Ajouter un produit</a>'),

            AssociationField::new('tickets', ' Ticket associé')
                ->onlyOnDetail(),
        ];
    }

    /**
     * Mettre à jour l'état des tickets liés à la réparation avec messages flash
     */
    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $flashBag = $this->requestStack->getSession()->getFlashBag();

        if ($entityInstance instanceof Reparation) {
            foreach ($entityInstance->getTickets() as $ticket) {
                if (strtolower($entityInstance->getStatutReparation()) === 'terminé') {
                    $ticket->setStatutTicket('Résolu');
                } else {
                    $ticket->setStatutTicket('En cours');
                }
                $entityManager->persist($ticket);
            }
            $entityManager->flush();
        }

        $flashBag->add('success', 'Réparation mise à jour avec succès.');
        parent::updateEntity($entityManager, $entityInstance);
    }

    /**
     * Vérifier avant suppression d'une réparation avec messages flash
     */
    public function deleteEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $flashBag = $this->requestStack->getSession()->getFlashBag();

        if ($entityInstance instanceof Reparation) {
            $ticket = $entityManager->getRepository(Ticket::class)->findOneBy(['reparation' => $entityInstance]);

            if ($ticket && $ticket->getStatutTicket() !== 'Résolu') {
                $flashBag->add('danger', 'Impossible de supprimer une réparation liée à un ticket non résolu.');
                return;
            }
        }

        $flashBag->add('success', 'Réparation supprimée avec succès.');
        parent::deleteEntity($entityManager, $entityInstance);
    }
}
