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

class ReparationCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Reparation::class;
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof Reparation) {
            return;
        }

        // Vérifier si le produit est bien sélectionné
        $produit = $entityInstance->getProduit();
        
        if (!$produit) {
            throw new \Exception("Vous devez sélectionner un produit pour la réparation.");
        }

        // Vérifier si le produit existe dans la base de données
        $produitExist = $entityManager->getRepository(Produit::class)->find($produit->getId());

        if (!$produitExist) {
            throw new \Exception("Le produit sélectionné n'existe pas.");
        }

        $entityInstance->setProduit($produitExist);
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

            // 🔥 Correction : Afficher uniquement les produits destinés à la réparation
            AssociationField::new('produit')
                ->setLabel('Produit concerné')
                ->setQueryBuilder(function ($qb) {
                    return $qb->where('entity.typeProduit = :type')
                              ->setParameter('type', 'réparation');
                })
                ->autocomplete(),
        ];
    }
}
