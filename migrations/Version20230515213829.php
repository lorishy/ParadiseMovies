<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230515213829 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE acteur ADD serie_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE acteur ADD CONSTRAINT FK_EAFAD362D94388BD FOREIGN KEY (serie_id) REFERENCES serie (id)');
        $this->addSql('CREATE INDEX IDX_EAFAD362D94388BD ON acteur (serie_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE acteur DROP FOREIGN KEY FK_EAFAD362D94388BD');
        $this->addSql('DROP INDEX IDX_EAFAD362D94388BD ON acteur');
        $this->addSql('ALTER TABLE acteur DROP serie_id');
    }
}
