<?php

namespace App\Controller\Admin;

use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Response;

class UtilisateurCrudController extends AbstractCrudController
{
    private UserPasswordHasherInterface $passwordHasher;
    private FlashBagInterface $flashBag;

    public function __construct(UserPasswordHasherInterface $passwordHasher, RequestStack $requestStack)
    {
        $this->passwordHasher = $passwordHasher;
        $this->flashBag = $requestStack->getSession()->getFlashBag();
    }

    public static function getEntityFqcn(): string
    {
        return Utilisateur::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('nomUtilisateur', 'Nom'),
            TextField::new('prenomUtilisateur', 'Prénom'),
            EmailField::new('email', 'Email'),
            TextField::new('adresse', 'Adresse'),
            TextField::new('numTelephone', 'Téléphone'),
        ];
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance instanceof Utilisateur) {
            if ($entityInstance->getPassword()) {
                $hashedPassword = $this->passwordHasher->hashPassword($entityInstance, $entityInstance->getPassword());
                $entityInstance->setPassword($hashedPassword);
            }
        }

        parent::persistEntity($entityManager, $entityInstance);
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance instanceof Utilisateur) {
            $originalPassword = $entityManager->getUnitOfWork()->getOriginalEntityData($entityInstance)['password'] ?? null;

            if ($entityInstance->getPassword()) {
                $hashedPassword = $this->passwordHasher->hashPassword($entityInstance, $entityInstance->getPassword());
                $entityInstance->setPassword($hashedPassword);
            } else {
                $entityInstance->setPassword($originalPassword);
            }
        }

        parent::updateEntity($entityManager, $entityInstance);
    }

    public function deleteEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance instanceof Utilisateur) {
            $hasReparations = $entityManager->getRepository(\App\Entity\Reparation::class)
                ->findOneBy(['utilisateur' => $entityInstance]);

            $hasTickets = $entityManager->getRepository(\App\Entity\Ticket::class)
                ->findOneBy(['utilisateur' => $entityInstance]);

            $hasRendezVous = $entityManager->getRepository(\App\Entity\RendezVous::class)
                ->findOneBy(['utilisateur' => $entityInstance]);

            if ($hasReparations || $hasTickets || $hasRendezVous) {
                $this->addFlash('danger', 'impossible de supprimer un client liée à une réparation en cours.');
                return;
            }

            $this->flashBag->add('success', 'Utilisateur supprimé avec succès.');
        }

        parent::deleteEntity($entityManager, $entityInstance);
    }
}
