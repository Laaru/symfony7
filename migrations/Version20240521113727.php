<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240521113727 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE product_store (product_id INT NOT NULL, store_id INT NOT NULL, PRIMARY KEY(product_id, store_id))');
        $this->addSql('CREATE INDEX IDX_5E0B232B4584665A ON product_store (product_id)');
        $this->addSql('CREATE INDEX IDX_5E0B232BB092A811 ON product_store (store_id)');
        $this->addSql('ALTER TABLE product_store ADD CONSTRAINT FK_5E0B232B4584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE product_store ADD CONSTRAINT FK_5E0B232BB092A811 FOREIGN KEY (store_id) REFERENCES store (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE product_store DROP CONSTRAINT FK_5E0B232B4584665A');
        $this->addSql('ALTER TABLE product_store DROP CONSTRAINT FK_5E0B232BB092A811');
        $this->addSql('DROP TABLE product_store');
    }
}
