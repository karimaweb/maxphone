<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250307150829 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE historique_reparation DROP FOREIGN KEY FK_F78B9B7497934BA');
        $this->addSql('ALTER TABLE historique_reparation CHANGE statut statut_historique_reparation VARCHAR(255) NOT NULL, CHANGE date date_maj_reparation DATETIME NOT NULL');
        $this->addSql('ALTER TABLE historique_reparation ADD CONSTRAINT FK_F78B9B7497934BA FOREIGN KEY (reparation_id) REFERENCES reparation (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE historique_reparation DROP FOREIGN KEY FK_F78B9B7497934BA');
        $this->addSql('ALTER TABLE historique_reparation CHANGE statut_historique_reparation statut VARCHAR(255) NOT NULL, CHANGE date_maj_reparation date DATETIME NOT NULL');
        $this->addSql('ALTER TABLE historique_reparation ADD CONSTRAINT FK_F78B9B7497934BA FOREIGN KEY (reparation_id) REFERENCES reparation (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
    }
}
