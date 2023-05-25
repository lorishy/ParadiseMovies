<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230525172950 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE acteur ADD image VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE film ADD date_sortie DATE NOT NULL');
        $this->addSql('ALTER TABLE serie ADD date_sortie DATE NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE film DROP date_sortie');
        $this->addSql('ALTER TABLE acteur DROP image');
        $this->addSql('ALTER TABLE serie DROP date_sortie');
    }
}
