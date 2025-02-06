<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250206132307 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE rendez_vous (id INT AUTO_INCREMENT NOT NULL, utilisateur_id INT DEFAULT NULL, date_heure_rendez_vous DATETIME NOT NULL, statut_rendez_vous VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, INDEX IDX_65E8AA0AFB88E14F (utilisateur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE rendez_vous ADD CONSTRAINT FK_65E8AA0AFB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE reparation ADD rendez_vous_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE reparation ADD CONSTRAINT FK_8FDF219D91EF7EAA FOREIGN KEY (rendez_vous_id) REFERENCES rendez_vous (id)');
        $this->addSql('CREATE INDEX IDX_8FDF219D91EF7EAA ON reparation (rendez_vous_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reparation DROP FOREIGN KEY FK_8FDF219D91EF7EAA');
        $this->addSql('ALTER TABLE rendez_vous DROP FOREIGN KEY FK_65E8AA0AFB88E14F');
        $this->addSql('DROP TABLE rendez_vous');
        $this->addSql('DROP INDEX IDX_8FDF219D91EF7EAA ON reparation');
        $this->addSql('ALTER TABLE reparation DROP rendez_vous_id');
    }
}
