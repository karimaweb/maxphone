<?php

namespace App\Form;

use App\Entity\Utilisateur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nomUtilisateur', TextType::class, [
                'label' => 'Nom',
                'attr' => ['placeholder' => 'Votre nom'],
                'required' => true,
            ])
            ->add('prenomUtilisateur', TextType::class, [
                'label' => 'Prénom',
                'attr' => ['placeholder' => 'Votre prénom'],
                'required' => true,
            ])
            ->add('email', EmailType::class, [
                'label' => 'Adresse Email',
                'attr' => ['placeholder' => 'Votre email'],
                'required' => true,
            ])
            ->add('password', PasswordType::class, [
                'label' => 'Mot de passe',
                'attr' => ['placeholder' => 'Votre mot de passe'],
                'required' => true,
            ])
            ->add('adresse', TextType::class, [
                'label' => 'Adresse',
                'attr' => ['placeholder' => 'Votre adresse'],
                'required' => true,
            ])
            ->add('numTelephone', TextType::class, [
                'label' => 'Numéro de téléphone',
                'attr' => ['placeholder' => 'Votre numéro de téléphone'],
                'required' => true,
            ])
            ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Utilisateur::class,
        ]);
    }
}