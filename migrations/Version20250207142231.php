<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250207142231 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE produit DROP FOREIGN KEY FK_29A5EC27700047D2');
        $this->addSql('ALTER TABLE produit DROP FOREIGN KEY FK_29A5EC2797934BA');
        $this->addSql('DROP INDEX IDX_29A5EC27700047D2 ON produit');
        $this->addSql('DROP INDEX IDX_29A5EC2797934BA ON produit');
        $this->addSql('ALTER TABLE produit DROP reparation_id, DROP ticket_id');
        $this->addSql('ALTER TABLE ticket CHANGE date_creation_ticket date_creation_ticket DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ticket CHANGE date_creation_ticket date_creation_ticket DATETIME NOT NULL');
        $this->addSql('ALTER TABLE produit ADD reparation_id INT DEFAULT NULL, ADD ticket_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE produit ADD CONSTRAINT FK_29A5EC27700047D2 FOREIGN KEY (ticket_id) REFERENCES utilisateur (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE produit ADD CONSTRAINT FK_29A5EC2797934BA FOREIGN KEY (reparation_id) REFERENCES reparation (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_29A5EC27700047D2 ON produit (ticket_id)');
        $this->addSql('CREATE INDEX IDX_29A5EC2797934BA ON produit (reparation_id)');
    }
}
