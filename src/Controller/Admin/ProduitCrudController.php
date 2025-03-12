<?php

namespace App\Controller\Admin;

use App\Entity\Produit;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ProduitCrudController extends AbstractCrudController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public static function getEntityFqcn(): string
    {
        return Produit::class;
    }

    // Récupérer uniquement les produits en vente dans l'index
    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        return $this->entityManager->getRepository(Produit::class)->createQueryBuilder('p')
            ->where('p.typeProduit = :type')
            ->setParameter('type', 'vente');
    }
   


    //  Configuration des champs avec validation
    public function configureFields(string $pageName): iterable
    {
        $typeProduitField = ChoiceField::new('typeProduit', 'Type Produit')
            ->setChoices(['Vente' => 'vente', 'Réparation' => 'réparation'])
            ->setRequired(true);

        $fields = [
            // IdField::new('id')->hideOnForm(),
            TextField::new('libelleProduit', 'Nom du produit')
                ->setRequired(true)
                ->setHelp('Entrez un nom valide sans caractères spéciaux.')
                ->setMaxLength(100)
                ->formatValue(function ($value, $entity) {
                    return sprintf(
                        '<a href="%s">%s</a>',
                        $this->generateUrl('produit_detail', ['id' => $entity->getId()]),
                        htmlspecialchars($value)
                    );
                })
                ->renderAsHtml(),

            $typeProduitField,
            AssociationField::new('categorie', 'Catégorie')
                ->setRequired(true)
                ->setHelp('Sélectionnez une catégorie existante.'),

            //  Ajout du prix unitaire 
            NumberField::new('prixUnitaire', 'Prix Unitaire (€)')
                ->setHelp('Prix du produit en euros.')
                ->setNumDecimals(2)
                ->setStoredAsString(),

            // Ajout de la quantité en stock avec des alertes visuelles
            TextField::new('formattedStock', 'Stock')
            ->formatValue(function ($value, $entity) {
                return $entity->getFormattedStock(); // Appel correct de la méthode
            })
            ->renderAsHtml() // Permet d'afficher du HTML dans le tableau
            ->onlyOnIndex(), // S'affiche seulement sur la liste des produits
        ];

        //  Si l'utilisateur est en train d'ajouter ou de modifier un produit
        if ($pageName === 'edit' || $pageName === 'new') {
            $fields[] = NumberField::new('prixUnitaire', 'Prix Unitaire')
                ->setRequired(false)
                ->setHelp('Le prix doit être positif et ne pas dépasser 2000.')
                ->onlyOnForms();

            $fields[] = NumberField::new('qteStock', 'Quantité en Stock')
                ->setRequired(false)
                ->setHelp('La quantité ne peut pas être négative.')
                ->onlyOnForms();
        }

        return $fields;
    }
        //  Vérification avant d'ajouter un produit (CREATE) 
    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof Produit) {
            return;
        }

        // Validation des champs
        if (empty($entityInstance->getLibelleProduit()) || strlen($entityInstance->getLibelleProduit()) < 3) {
            $this->addFlash('danger', 'Le nom du produit doit contenir au moins 3 caractères.');
            return;
        }

        if ($entityInstance->getPrixUnitaire() !== null && $entityInstance->getPrixUnitaire() < 0) {
            $this->addFlash('danger', 'Le prix du produit ne peut pas être négatif.');
            return;
        }
        //  Vérification : Empêcher les doublons de produits
        $existingProduct = $entityManager->getRepository(Produit::class)->findOneBy([
            'libelleProduit' => $entityInstance->getLibelleProduit()
        ]);

        if ($existingProduct) {
            $this->addFlash('danger', 'Ce produit existe déjà.');
            return; //  Stoppe l'ajout du produit
        }

        if ($entityInstance->getPrixUnitaire() > 10000) {
            $this->addFlash('danger', 'Le prix ne peut pas dépasser 2000.');
            return;
        }

        if ($entityInstance->getQteStock() !== null && $entityInstance->getQteStock() < 0) {
            $this->addFlash('danger', 'La quantité en stock ne peut pas être négative.');
            return;
        }
        if (!$entityInstance->getTypeProduit()) {
            $entityInstance->setTypeProduit('vente'); // Par défaut, un produit est en vente
        }
    
        if ($entityInstance->getQteStock() !== null && $entityInstance->getQteStock() < 0) {
            $this->addFlash('danger', 'La quantité en stock ne peut pas être négative.');
            return;
        }
    
        // 🔹 Assurer que le type du produit est bien défini
        if (!$entityInstance->getTypeProduit()) {
            $entityInstance->setTypeProduit('vente'); // Par défaut, un produit est en vente
        }
    
    
        // Sauvegarde
        $entityManager->persist($entityInstance);
        $entityManager->flush();
        // affichage des messages Flash
        $this->addFlash('success', 'Le produit a été ajouté avec succès.');
    }
    

    //  Vérification avant modification (UPDATE)
    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof Produit) {
            return;
        }

        // Vérifications similaires à persistEntity()
        if (empty($entityInstance->getLibelleProduit()) || strlen($entityInstance->getLibelleProduit()) < 3) {
            $this->addFlash('danger', 'Le nom du produit doit contenir au moins 3 caractères.');
            return;
        }

        if ($entityInstance->getPrixUnitaire() !== null && $entityInstance->getPrixUnitaire() < 0) {
            $this->addFlash('danger', 'Le prix du produit ne peut pas être négatif.');
            return;
        }

        if ($entityInstance->getPrixUnitaire() > 10000) {
            $this->addFlash('danger', 'Le prix ne peut pas dépasser 2000.');
            return;
        }

        if ($entityInstance->getQteStock() !== null && $entityInstance->getQteStock() < 0) {
            $this->addFlash('danger', 'La quantité en stock ne peut pas être négative.');
            return;
        }

        // Sauvegarde des modifications
        $entityManager->flush();
        $this->addFlash('success', 'Le produit a été mis à jour avec succès.');
    }

    //  Vérification avant suppression (DELETE)
    public function deleteEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof Produit) {
            return;
        }

        // Vérifier si le produit est encore en stock avant suppression
        if ($entityInstance->getQteStock() > 0) {
            $this->addFlash('danger', 'Impossible de supprimer un produit encore en stock.');
            return;
        }

        // Suppression autorisée
        $entityManager->remove($entityInstance);
        $entityManager->flush();

        $this->addFlash('success', 'Le produit a été supprimé avec succès.');
    }
}
