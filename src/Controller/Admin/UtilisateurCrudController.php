<?php

namespace App\Controller\Admin;

use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

class UtilisateurCrudController extends AbstractCrudController
{
    private UserPasswordHasherInterface $passwordHasher;
    private FlashBagInterface $flashBag;

    public function __construct(UserPasswordHasherInterface $passwordHasher, RequestStack $requestStack)
    {
        $this->passwordHasher = $passwordHasher;
        $this->flashBag = $requestStack->getSession()->getFlashBag();
    }
    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setPageTitle('index', 'Les Utilisateurs ');
    }
    public static function getEntityFqcn(): string
    {
        return Utilisateur::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('nomUtilisateur', 'Nom')
                ->setRequired(true)
                ->setHelp('Ce champ est obligatoire et doit contenir  au moins 3 caractères.')
                ->setFormTypeOptions([
                    'constraints' => [
                        new Assert\NotBlank(['message' => 'Le nom est obligatoire et doit contenir  au moins 3 caractères.']),
                        new Assert\Length(['min' => 3, 'minMessage' => 'Le nom doit contenir au moins 3 caractères.']),
                        new Assert\Regex([
                            'pattern' => '/^[a-zA-ZÀ-ÿ\- ]+$/',
                            'message' => 'Le nom ne doit contenir que des lettres et des espaces.'
                        ])
                    ]
                ]),

            TextField::new('prenomUtilisateur', 'Prénom')
                ->setRequired(true)
                ->setHelp('Ce champ est obligatoire et doit contenir  au moins 3 caractères.')
                ->setFormTypeOptions([
                    'constraints' => [
                        new Assert\NotBlank(['message' => 'Le prénom est obligatoire.']),
                        new Assert\Length(['min' => 3, 'minMessage' => 'Le prénom doit contenir au moins 3 caractères.']),
                        new Assert\Regex([
                            'pattern' => '/^[a-zA-ZÀ-ÿ\- ]+$/',
                            'message' => 'Le prénom ne doit contenir que des lettres et des espaces.'
                        ])
                    ]
                ]),

            EmailField::new('email', 'Email')
                ->setRequired(true)
                ->setHelp('Ce champ est obligatoire et doit être une adresse e-mail valide.')
                ->setFormTypeOptions([
                    'constraints' => [
                        new Assert\NotBlank(['message' => 'L’email est obligatoire.']),
                        new Assert\Email(['message' => 'Veuillez saisir une adresse e-mail valide.'])
                    ]
                ]),

            TextField::new('password', 'Mot de passe')
                ->setFormType(PasswordType::class)
                ->setRequired(true)
                ->setHelp('Le mot de passe doit contenir au moins 8 caractères.')
                ->setFormTypeOptions([
                    'constraints' => [
                        new Assert\NotBlank(['message' => 'Le mot de passe est obligatoire.']),
                        new Assert\Length([
                            'min' => 6,
                            'minMessage' => 'Le mot de passe doit contenir au moins 8 caractères avec au moins une lettre et un chiffre.'
                        ])
                    ]
                ])
                ->onlyOnForms(),

            TextField::new('adresse', 'Adresse')
                ->setRequired(true)
                ->setHelp('L’adresse doit contenir au moins 10 caractères.')
                ->setFormTypeOptions([
                    'constraints' => [
                        new Assert\NotBlank(['message' => 'L’adresse est obligatoire.']),
                        new Assert\Length([
                            'min' => 10,
                            'minMessage' => 'L’adresse doit contenir au moins 10 caractères.'
                        ])
                    ]
                ]),

            TextField::new('numTelephone', 'Téléphone')
                ->setRequired(true)
                ->setHelp('Doit être un numéro valide (ex: 0601020304).')
                ->setFormTypeOptions([
                    'constraints' => [
                        new Assert\NotBlank(['message' => 'Le numéro de téléphone est obligatoire.']),
                        new Assert\Regex([
                            'pattern' => '/^0[1-9]([-. ]?[0-9]{2}){4}$/',
                            'message' => 'Le numéro de téléphone doit être valide (ex: 0601020304).'
                        ])
                    ]
                ]),
        ];
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance instanceof Utilisateur) {
            if ($entityInstance->getPassword()) {
                $hashedPassword = $this->passwordHasher->hashPassword($entityInstance, $entityInstance->getPassword());
                $entityInstance->setPassword($hashedPassword);
            }

            parent::persistEntity($entityManager, $entityInstance);

            $this->addFlash('success', sprintf(
                'Utilisateur <strong>%s %s</strong> ajouté avec succès ! <br> 
                <a href="/admin?crudAction=new&crudControllerFqcn=App\Controller\Admin\ReparationCrudController&lastClientId=%d" 
                class="btn btn-primary mt-2">Créer une réparation pour ce client</a>',
                $entityInstance->getNomUtilisateur(),
                $entityInstance->getPrenomUtilisateur(),
                $entityInstance->getId()
            ));
        }
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

            parent::updateEntity($entityManager, $entityInstance);
            $this->addFlash('success', 'Utilisateur modifié avec succès.');
        }
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
                $this->addFlash('danger', 'Impossible de supprimer un client lié à une réparation en cours.');
                return;
            }

            parent::deleteEntity($entityManager, $entityInstance);
            $this->addFlash('success', 'Utilisateur supprimé avec succès.');
        }
    }
}
