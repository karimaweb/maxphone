<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250206124850 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE reparation (id INT AUTO_INCREMENT NOT NULL, diagnostic VARCHAR(255) NOT NULL, date_heure_reparation DATETIME NOT NULL, statut_reparation VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE produit ADD reparation_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE produit ADD CONSTRAINT FK_29A5EC2797934BA FOREIGN KEY (reparation_id) REFERENCES reparation (id)');
        $this->addSql('CREATE INDEX IDX_29A5EC2797934BA ON produit (reparation_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE produit DROP FOREIGN KEY FK_29A5EC2797934BA');
        $this->addSql('DROP TABLE reparation');
        $this->addSql('DROP INDEX IDX_29A5EC2797934BA ON produit');
        $this->addSql('ALTER TABLE produit DROP reparation_id');
    }
}
