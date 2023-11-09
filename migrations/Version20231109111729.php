<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231109111729 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE event ADD event_location_id INT NOT NULL');
        //$this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA7ADC4F20E FOREIGN KEY (event_location_id) REFERENCES location (id)');
        $this->addSql('CREATE INDEX IDX_3BAE0AA7ADC4F20E ON event (event_location_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        //$this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA7ADC4F20E');
        $this->addSql('DROP INDEX IDX_3BAE0AA7ADC4F20E ON event');
        $this->addSql('ALTER TABLE event DROP event_location_id');
    }
}
