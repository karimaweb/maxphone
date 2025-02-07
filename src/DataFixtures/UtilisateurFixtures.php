<?php

namespace App\DataFixtures;

use App\Entity\Utilisateur;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UtilisateurFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Liste des utilisateurs avec leurs informations
        $utilisateurs = [
            [
                'nom' => 'Dupont',
                'prenom' => 'Jean',
                'email' => 'jean.dupont@example.com',
                'password' => 'password123', // Utilisez un encodeur pour les mots de passe réels
                'roles' => ['ROLE_ADMIN'],
                'adresse' => '10 Rue des Lilas, Paris',
                'telephone' => '0102030405',
            ],
            [
                'nom' => 'Durand',
                'prenom' => 'Marie',
                'email' => 'marie.durand@example.com',
                'password' => 'password456',
                'roles' => ['ROLE_USER'],
                'adresse' => '15 Avenue des Champs, Lyon',
                'telephone' => '0607080910',
            ],
            [
                'nom' => 'Martin',
                'prenom' => 'Paul',
                'email' => 'paul.martin@example.com',
                'password' => 'password789',
                'roles' => ['ROLE_USER'],
                'adresse' => '5 Impasse des Roses, Marseille',
                'telephone' => '0708091011',
            ],
        ];

        // Création des utilisateurs
        foreach ($utilisateurs as $index => $data) {
            $utilisateur = new Utilisateur();
            $utilisateur->setNomUtilisateur($data['nom']);
            $utilisateur->setPrenomUtilisateur($data['prenom']);
            $utilisateur->setEmail($data['email']);
            $utilisateur->setPassword($data['password']); // Encodez dans un vrai projet
            $utilisateur->setRoles($data['roles']);
            $utilisateur->setAdresse($data['adresse']);
            $utilisateur->setNumTelephone($data['telephone']);

            $manager->persist($utilisateur);

            // Ajoutez une référence pour les utiliser dans d'autres fixtures
            $this->addReference('utilisateur_' . $index, $utilisateur);
        }

        $manager->flush();
    }
}
