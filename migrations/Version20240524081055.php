<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240524081055 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE log_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE log (id INT NOT NULL, message TEXT DEFAULT NULL, context TEXT DEFAULT NULL, level SMALLINT DEFAULT NULL, level_name VARCHAR(50) DEFAULT NULL, channel VARCHAR(255) DEFAULT NULL, extra TEXT DEFAULT NULL, datetime TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, formatted TEXT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN log.context IS \'(DC2Type:array)\'');
        $this->addSql('COMMENT ON COLUMN log.extra IS \'(DC2Type:array)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE log_id_seq CASCADE');
        $this->addSql('DROP TABLE log');
    }
}
