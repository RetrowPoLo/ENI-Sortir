<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231108091017 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE event ADD state VARCHAR(255) NOT NULL, ADD nb_inscription_max INT NOT NULL');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649DDF76323');
        $this->addSql('DROP INDEX IDX_8D93D649DDF76323 ON user');
        $this->addSql('ALTER TABLE user DROP sites_no_site_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE event DROP state, DROP nb_inscription_max');
        $this->addSql('ALTER TABLE `user` ADD sites_no_site_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE `user` ADD CONSTRAINT FK_8D93D649DDF76323 FOREIGN KEY (sites_no_site_id) REFERENCES location_site (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_8D93D649DDF76323 ON `user` (sites_no_site_id)');
    }
}
