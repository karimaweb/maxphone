<?php

namespace App\Form;

use App\Entity\Utilisateur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Validator\Constraints as Assert;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nomUtilisateur', TextType::class, [
                'label' => 'Nom',
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le nom est obligatoire.']),
                    new Assert\Length([
                        'min' => 3,
                        'max' => 100,
                        'minMessage' => 'Le nom doit contenir au moins {{ limit }} caractères.',
                        'maxMessage' => 'Le nom ne peut pas dépasser {{ limit }} caractères.'
                    ]),
                ],
                'attr' => ['class' => 'form-control']
            ])
            ->add('prenomUtilisateur', TextType::class, [
                'label' => 'Prénom',
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le prénom est obligatoire.']),
                    new Assert\Length([
                        'min' => 3,
                        'max' => 100,
                        'minMessage' => 'Le prénom doit contenir au moins {{ limit }} caractères.',
                        'maxMessage' => 'Le prénom ne peut pas dépasser {{ limit }} caractères.'
                    ]),
                ],
                'attr' => ['class' => 'form-control']
            ])
            ->add('email', EmailType::class, [
                'label' => 'Adresse Email',
                'constraints' => [
                    new Assert\NotBlank(['message' => 'L\'adresse email est obligatoire.']),
                    new Assert\Email(['message' => 'Veuillez entrer une adresse email valide.']),
                ],
                'attr' => ['class' => 'form-control']
            ])
            ->add('numTelephone', TelType::class, [
                'label' => 'Numéro de téléphone',
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le numéro de téléphone est obligatoire.']),
                    new Assert\Regex([
                        'pattern' => '/^\d{10}$/',
                        'message' => 'Le numéro de téléphone doit contenir 10 chiffres.'
                    ])
                ],
                'attr' => ['class' => 'form-control']
            ])
            ->add('adresse', TextType::class, [
                'label' => 'Adresse',
                'constraints' => [
                    new Assert\NotBlank(['message' => 'L\'adresse est obligatoire.']),
                    new Assert\Length([
                        'min' => 5,
                        'max' => 255,
                        'minMessage' => 'L\'adresse doit contenir au moins {{ limit }} caractères.',
                        'maxMessage' => 'L\'adresse ne peut pas dépasser {{ limit }} caractères.'
                    ])
                ],
                'attr' => ['class' => 'form-control']
            ])
            ->add('password', PasswordType::class, [
                'label' => 'Mot de passe',
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le mot de passe est obligatoire.']),
                    new Assert\Length([
                        'min' => 8,
                        'max' => 50,
                        'minMessage' => 'Le mot de passe doit contenir au moins {{ limit }} caractères.',
                        'maxMessage' => 'Le mot de passe ne peut pas dépasser {{ limit }} caractères.'
                    ]),
                    new Assert\Regex([
                        'pattern' => '/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d@$!%*?&]{8,}$/',
                        'message' => 'Le mot de passe doit contenir au moins 8 caractères, avec au moins une lettre et un chiffre.'
                    ])
                ],
                'attr' => ['class' => 'form-control']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Utilisateur::class,
        ]);
    }
}
