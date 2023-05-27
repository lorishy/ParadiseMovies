<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230527204856 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE serie_categorie (serie_id INT NOT NULL, categorie_id INT NOT NULL, INDEX IDX_6137E805D94388BD (serie_id), INDEX IDX_6137E805BCF5E72D (categorie_id), PRIMARY KEY(serie_id, categorie_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE serie_categorie ADD CONSTRAINT FK_6137E805D94388BD FOREIGN KEY (serie_id) REFERENCES serie (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE serie_categorie ADD CONSTRAINT FK_6137E805BCF5E72D FOREIGN KEY (categorie_id) REFERENCES categorie (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE serie_categorie DROP FOREIGN KEY FK_6137E805D94388BD');
        $this->addSql('ALTER TABLE serie_categorie DROP FOREIGN KEY FK_6137E805BCF5E72D');
        $this->addSql('DROP TABLE serie_categorie');
    }
}
