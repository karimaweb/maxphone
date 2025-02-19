<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250219151241 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reparation DROP FOREIGN KEY FK_8FDF219D700047D2');
        $this->addSql('DROP INDEX IDX_8FDF219D700047D2 ON reparation');
        $this->addSql('ALTER TABLE reparation DROP ticket_id');
        $this->addSql('ALTER TABLE ticket DROP FOREIGN KEY FK_97A0ADA397934BA');
        $this->addSql('ALTER TABLE ticket CHANGE reparation_id reparation_id INT NOT NULL');
        $this->addSql('ALTER TABLE ticket ADD CONSTRAINT FK_97A0ADA397934BA FOREIGN KEY (reparation_id) REFERENCES reparation (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reparation ADD ticket_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE reparation ADD CONSTRAINT FK_8FDF219D700047D2 FOREIGN KEY (ticket_id) REFERENCES ticket (id) ON UPDATE NO ACTION ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_8FDF219D700047D2 ON reparation (ticket_id)');
        $this->addSql('ALTER TABLE ticket DROP FOREIGN KEY FK_97A0ADA397934BA');
        $this->addSql('ALTER TABLE ticket CHANGE reparation_id reparation_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE ticket ADD CONSTRAINT FK_97A0ADA397934BA FOREIGN KEY (reparation_id) REFERENCES reparation (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
    }
}
