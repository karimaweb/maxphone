<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250216114022 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE produit CHANGE prix_unitaire prix_unitaire DOUBLE PRECISION DEFAULT NULL, CHANGE qte_stock qte_stock INT DEFAULT NULL');
        $this->addSql('ALTER TABLE reparation ADD CONSTRAINT FK_8FDF219DF347EFB FOREIGN KEY (produit_id) REFERENCES produit (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_8FDF219DF347EFB ON reparation (produit_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reparation DROP FOREIGN KEY FK_8FDF219DF347EFB');
        $this->addSql('DROP INDEX IDX_8FDF219DF347EFB ON reparation');
        $this->addSql('ALTER TABLE produit CHANGE prix_unitaire prix_unitaire DOUBLE PRECISION NOT NULL, CHANGE qte_stock qte_stock INT NOT NULL');
    }
}
