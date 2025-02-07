<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250207142845 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE produit ADD attribuer_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE produit ADD CONSTRAINT FK_29A5EC2726A8FFEE FOREIGN KEY (attribuer_id) REFERENCES reparation (id)');
        $this->addSql('CREATE INDEX IDX_29A5EC2726A8FFEE ON produit (attribuer_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE produit DROP FOREIGN KEY FK_29A5EC2726A8FFEE');
        $this->addSql('DROP INDEX IDX_29A5EC2726A8FFEE ON produit');
        $this->addSql('ALTER TABLE produit DROP attribuer_id');
    }
}
