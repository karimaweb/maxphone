<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250206131133 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE utilisateur (id INT AUTO_INCREMENT NOT NULL, nom_utilsateur VARCHAR(255) NOT NULL, prenom_utilisateur VARCHAR(255) NOT NULL, email_utilisateur VARCHAR(255) NOT NULL, motde_passe VARCHAR(255) NOT NULL, role VARCHAR(255) NOT NULL, num_telephone VARCHAR(255) NOT NULL, adresse VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE produit ADD utilisateur_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE produit ADD CONSTRAINT FK_29A5EC27FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id)');
        $this->addSql('CREATE INDEX IDX_29A5EC27FB88E14F ON produit (utilisateur_id)');
        $this->addSql('ALTER TABLE ticket ADD utilisateur_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE ticket ADD CONSTRAINT FK_97A0ADA3FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id)');
        $this->addSql('CREATE INDEX IDX_97A0ADA3FB88E14F ON ticket (utilisateur_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE produit DROP FOREIGN KEY FK_29A5EC27FB88E14F');
        $this->addSql('ALTER TABLE ticket DROP FOREIGN KEY FK_97A0ADA3FB88E14F');
        $this->addSql('DROP TABLE utilisateur');
        $this->addSql('DROP INDEX IDX_97A0ADA3FB88E14F ON ticket');
        $this->addSql('ALTER TABLE ticket DROP utilisateur_id');
        $this->addSql('DROP INDEX IDX_29A5EC27FB88E14F ON produit');
        $this->addSql('ALTER TABLE produit DROP utilisateur_id');
    }
}
