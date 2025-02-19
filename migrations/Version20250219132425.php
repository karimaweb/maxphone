<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250219132425 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reparation ADD ticket_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE reparation ADD CONSTRAINT FK_8FDF219D700047D2 FOREIGN KEY (ticket_id) REFERENCES ticket (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_8FDF219D700047D2 ON reparation (ticket_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reparation DROP FOREIGN KEY FK_8FDF219D700047D2');
        $this->addSql('DROP INDEX IDX_8FDF219D700047D2 ON reparation');
        $this->addSql('ALTER TABLE reparation DROP ticket_id');
    }
}
