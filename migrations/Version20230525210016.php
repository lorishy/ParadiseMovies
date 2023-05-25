<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230525210016 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE serie (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, image VARCHAR(255) DEFAULT NULL, date_sortie DATE NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE serie_acteur (serie_id INT NOT NULL, acteur_id INT NOT NULL, INDEX IDX_1D50880BD94388BD (serie_id), INDEX IDX_1D50880BDA6F574A (acteur_id), PRIMARY KEY(serie_id, acteur_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE serie_acteur ADD CONSTRAINT FK_1D50880BD94388BD FOREIGN KEY (serie_id) REFERENCES serie (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE serie_acteur ADD CONSTRAINT FK_1D50880BDA6F574A FOREIGN KEY (acteur_id) REFERENCES acteur (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE acteur DROP FOREIGN KEY FK_EAFAD362D94388BD');
        $this->addSql('DROP INDEX IDX_EAFAD362D94388BD ON acteur');
        $this->addSql('ALTER TABLE acteur DROP serie_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE episode DROP FOREIGN KEY FK_DDAA1CDAD94388BD');
        $this->addSql('ALTER TABLE serie_acteur DROP FOREIGN KEY FK_1D50880BD94388BD');
        $this->addSql('ALTER TABLE serie_acteur DROP FOREIGN KEY FK_1D50880BDA6F574A');
        $this->addSql('DROP TABLE serie');
        $this->addSql('DROP TABLE serie_acteur');
        $this->addSql('ALTER TABLE acteur ADD serie_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE acteur ADD CONSTRAINT FK_EAFAD362D94388BD FOREIGN KEY (serie_id) REFERENCES serie (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_EAFAD362D94388BD ON acteur (serie_id)');
    }
}
