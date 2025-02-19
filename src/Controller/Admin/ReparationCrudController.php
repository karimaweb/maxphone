<?php

namespace App\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use App\Entity\Reparation;
use App\Entity\Produit;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ReparationCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Reparation::class;
    }

    //  Vérification et sauvegarde d'une réparation
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

    // Vérifier si le produit appartient bien à un utilisateur ayant un rendez-vous
    $rendezVous = $entityInstance->getRendezVous();
    if (!$rendezVous) {
        $this->addFlash('danger', 'Veuillez associer un rendez-vous à cette réparation.');
        return;
    }

    // Vérifier si le rendez-vous est confirmé
    if ($rendezVous->getStatutRendezVous() !== 'confirmé') {
        $this->addFlash('danger', 'Le rendez-vous doit être confirmé avant de créer une réparation.');
        return;
    }

    parent::persistEntity($entityManager, $entityInstance);
}


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

        //  Récupérer uniquement les produits destinés à la réparation
        AssociationField::new('produit', 'Produit en réparation')
    ->setQueryBuilder(function ($qb) {
        return $qb->andWhere('p.typeProduit = :type')
                  ->setParameter('type', 'réparation');
    })
    ->setRequired(true)
    ->setLabel('Produit en réparation')
    ->autocomplete(),

];
}
public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
{
    if ($entityInstance instanceof Reparation) {
        $ticketRepo = $entityManager->getRepository(Ticket::class);
        $ticketRepo->updateTicketStatus($entityInstance);
    }
    parent::updateEntity($entityManager, $entityInstance);
}
}