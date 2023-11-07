<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231107082824 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE city (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, zipcode VARCHAR(10) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE event (id INT AUTO_INCREMENT NOT NULL, location_site_event_id INT NOT NULL, user_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, start_date_time DATETIME NOT NULL, limit_date_inscription DATETIME NOT NULL, event_info LONGTEXT DEFAULT NULL, duration TIME NOT NULL, INDEX IDX_3BAE0AA7D7C1BC5E (location_site_event_id), INDEX IDX_3BAE0AA7A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE location (id INT AUTO_INCREMENT NOT NULL, city_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, street VARCHAR(255) NOT NULL, latitude DOUBLE PRECISION NOT NULL, longitude DOUBLE PRECISION NOT NULL, INDEX IDX_5E9E89CB8BAC62AF (city_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE location_site (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_event (user_id INT NOT NULL, event_id INT NOT NULL, INDEX IDX_D96CF1FFA76ED395 (user_id), INDEX IDX_D96CF1FF71F7E88B (event_id), PRIMARY KEY(user_id, event_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA7D7C1BC5E FOREIGN KEY (location_site_event_id) REFERENCES location_site (id)');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA7A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE location ADD CONSTRAINT FK_5E9E89CB8BAC62AF FOREIGN KEY (city_id) REFERENCES city (id)');
        $this->addSql('ALTER TABLE user_event ADD CONSTRAINT FK_D96CF1FFA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_event ADD CONSTRAINT FK_D96CF1FF71F7E88B FOREIGN KEY (event_id) REFERENCES event (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user CHANGE location_site_id location_site_user_id INT NOT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6492D6D6130 FOREIGN KEY (location_site_user_id) REFERENCES location_site (id)');
        $this->addSql('CREATE INDEX IDX_8D93D6492D6D6130 ON user (location_site_user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `user` DROP FOREIGN KEY FK_8D93D6492D6D6130');
        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA7D7C1BC5E');
        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA7A76ED395');
        $this->addSql('ALTER TABLE location DROP FOREIGN KEY FK_5E9E89CB8BAC62AF');
        $this->addSql('ALTER TABLE user_event DROP FOREIGN KEY FK_D96CF1FFA76ED395');
        $this->addSql('ALTER TABLE user_event DROP FOREIGN KEY FK_D96CF1FF71F7E88B');
        $this->addSql('DROP TABLE city');
        $this->addSql('DROP TABLE event');
        $this->addSql('DROP TABLE location');
        $this->addSql('DROP TABLE location_site');
        $this->addSql('DROP TABLE user_event');
        $this->addSql('DROP INDEX IDX_8D93D6492D6D6130 ON `user`');
        $this->addSql('ALTER TABLE `user` CHANGE location_site_user_id location_site_id INT NOT NULL');
    }
}