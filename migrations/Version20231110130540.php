<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231110130540 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE event ADD end_date_time DATETIME NOT NULL, DROP duration');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA7ADC4F20E FOREIGN KEY (event_location_id) REFERENCES location (id)');
        $this->addSql('ALTER TABLE user DROP force_change');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA7ADC4F20E');
        $this->addSql('ALTER TABLE event ADD duration INT NOT NULL, DROP end_date_time');
        $this->addSql('ALTER TABLE `user` ADD force_change INT NOT NULL');
    }
}
