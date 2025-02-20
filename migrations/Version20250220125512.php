<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250220125512 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reparation DROP FOREIGN KEY FK_8FDF219D91EF7EAA');
        $this->addSql('ALTER TABLE reparation CHANGE rendez_vous_id rendez_vous_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE reparation ADD CONSTRAINT FK_8FDF219D91EF7EAA FOREIGN KEY (rendez_vous_id) REFERENCES rendez_vous (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reparation DROP FOREIGN KEY FK_8FDF219D91EF7EAA');
        $this->addSql('ALTER TABLE reparation CHANGE rendez_vous_id rendez_vous_id INT NOT NULL');
        $this->addSql('ALTER TABLE reparation ADD CONSTRAINT FK_8FDF219D91EF7EAA FOREIGN KEY (rendez_vous_id) REFERENCES rendez_vous (id) ON UPDATE NO ACTION ON DELETE CASCADE');
    }
}
