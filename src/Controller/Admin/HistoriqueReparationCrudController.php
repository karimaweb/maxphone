<?php


namespace App\Controller\Admin;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use App\Entity\HistoriqueReparation;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use Doctrine\ORM\EntityManagerInterface;

class HistoriqueReparationCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return HistoriqueReparation::class;
    }

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    public function configureCrud(Crud $crud): Crud
{
    return $crud->setPageTitle('index', ' Historiques de Réparation');
}
    // je désactive le boutton créer historique
    public function configureActions(Actions $actions): Actions
    {
    return $actions
        ->disable(Action::NEW); // Désactive le bouton "Créer"
    }
    // méthode pour récuerer les statuts de réparation sans répétition 
    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        return $this->entityManager->createQueryBuilder()
            ->select('h')
            ->from(HistoriqueReparation::class, 'h')
            ->where('h.id = (SELECT MAX(h2.id) FROM App\Entity\HistoriqueReparation h2 WHERE h2.reparation = h.reparation)')
            ->orderBy('h.dateMajReparation', 'DESC');
    }
    

    public function configureFields(string $pageName): iterable
   
    {
        return [
            
            TextField::new('historiqueSimplifie', ' ')
            ->formatValue(function ($value, $entity) {
                $historique = $entity->getReparation()->getHistoriqueClientsSimplifie();
        
                // ✅ Si l'historique est vide, ne pas afficher cette réparation
                return !empty(trim($historique)) ? nl2br($historique) : null;
            })
            ->renderAsHtml()
            ->onlyOnIndex()
    ];
}

    

}