<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250307131959 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE historique_reparation (id INT AUTO_INCREMENT NOT NULL, reparation_id INT NOT NULL, statut VARCHAR(255) NOT NULL, commentaire LONGTEXT DEFAULT NULL, date DATETIME NOT NULL, INDEX IDX_F78B9B7497934BA (reparation_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE historique_reparation ADD CONSTRAINT FK_F78B9B7497934BA FOREIGN KEY (reparation_id) REFERENCES reparation (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE historique_reparation DROP FOREIGN KEY FK_F78B9B7497934BA');
        $this->addSql('DROP TABLE historique_reparation');
    }
}
