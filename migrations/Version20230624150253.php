<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230624150253 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_film (user_id INT NOT NULL, film_id INT NOT NULL, INDEX IDX_F8C5F2EBA76ED395 (user_id), INDEX IDX_F8C5F2EB567F5183 (film_id), PRIMARY KEY(user_id, film_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_serie (user_id INT NOT NULL, serie_id INT NOT NULL, INDEX IDX_48F8686CA76ED395 (user_id), INDEX IDX_48F8686CD94388BD (serie_id), PRIMARY KEY(user_id, serie_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_film ADD CONSTRAINT FK_F8C5F2EBA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_film ADD CONSTRAINT FK_F8C5F2EB567F5183 FOREIGN KEY (film_id) REFERENCES film (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_serie ADD CONSTRAINT FK_48F8686CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_serie ADD CONSTRAINT FK_48F8686CD94388BD FOREIGN KEY (serie_id) REFERENCES serie (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE avis CHANGE description description VARCHAR(1000) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_film DROP FOREIGN KEY FK_F8C5F2EBA76ED395');
        $this->addSql('ALTER TABLE user_film DROP FOREIGN KEY FK_F8C5F2EB567F5183');
        $this->addSql('ALTER TABLE user_serie DROP FOREIGN KEY FK_48F8686CA76ED395');
        $this->addSql('ALTER TABLE user_serie DROP FOREIGN KEY FK_48F8686CD94388BD');
        $this->addSql('DROP TABLE user_film');
        $this->addSql('DROP TABLE user_serie');
        $this->addSql('ALTER TABLE avis CHANGE description description VARCHAR(255) NOT NULL');
    }
}
