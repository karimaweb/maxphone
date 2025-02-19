<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250219092934 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Correction de la relation entre Reparation et RendezVous avec gestion des contraintes.';
    }

    public function up(Schema $schema): void
    {
        // Vérifier si des réparations existent avec un rendez-vous NULL avant de rendre la colonne NOT NULL
        $this->addSql('UPDATE reparation SET rendez_vous_id = (SELECT id FROM rendez_vous ORDER BY id LIMIT 1) WHERE rendez_vous_id IS NULL');

        // Supprimer l'ancienne contrainte de clé étrangère
        $this->addSql('ALTER TABLE reparation DROP FOREIGN KEY FK_8FDF219D91EF7EAA');

        // Modifier la colonne pour ne plus autoriser NULL (après la mise à jour)
        $this->addSql('ALTER TABLE reparation CHANGE rendez_vous_id rendez_vous_id INT NOT NULL');

        // Ajouter la nouvelle contrainte avec ON DELETE SET NULL pour éviter la suppression des réparations
        $this->addSql('ALTER TABLE reparation ADD CONSTRAINT FK_8FDF219D91EF7EAA FOREIGN KEY (rendez_vous_id) REFERENCES rendez_vous (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        // Annuler la modification en permettant de nouveau les NULL
        $this->addSql('ALTER TABLE reparation DROP FOREIGN KEY FK_8FDF219D91EF7EAA');
        $this->addSql('ALTER TABLE reparation CHANGE rendez_vous_id rendez_vous_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE reparation ADD CONSTRAINT FK_8FDF219D91EF7EAA FOREIGN KEY (rendez_vous_id) REFERENCES rendez_vous (id) ON DELETE CASCADE');
    }
}
