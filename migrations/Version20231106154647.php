<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231106154647 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA718F805E2');
        $this->addSql('DROP INDEX IDX_3BAE0AA718F805E2 ON event');
        $this->addSql('ALTER TABLE event CHANGE location_site_id location_site_event_id INT NOT NULL');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA7D7C1BC5E FOREIGN KEY (location_site_event_id) REFERENCES location_site (id)');
        $this->addSql('CREATE INDEX IDX_3BAE0AA7D7C1BC5E ON event (location_site_event_id)');
        $this->addSql('ALTER TABLE user CHANGE location_site_id location_site_user_id INT NOT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6492D6D6130 FOREIGN KEY (location_site_user_id) REFERENCES location_site (id)');
        $this->addSql('CREATE INDEX IDX_8D93D6492D6D6130 ON user (location_site_user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA7D7C1BC5E');
        $this->addSql('DROP INDEX IDX_3BAE0AA7D7C1BC5E ON event');
        $this->addSql('ALTER TABLE event CHANGE location_site_event_id location_site_id INT NOT NULL');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA718F805E2 FOREIGN KEY (location_site_id) REFERENCES location_site (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_3BAE0AA718F805E2 ON event (location_site_id)');
        $this->addSql('ALTER TABLE `user` DROP FOREIGN KEY FK_8D93D6492D6D6130');
        $this->addSql('DROP INDEX IDX_8D93D6492D6D6130 ON `user`');
        $this->addSql('ALTER TABLE `user` CHANGE location_site_user_id location_site_id INT NOT NULL');
    }
}
