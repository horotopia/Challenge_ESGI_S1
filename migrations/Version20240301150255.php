<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240301150255 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE email_attachment_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE email_log_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE email_template_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE email_attachment (id INT NOT NULL, file_name VARCHAR(255) NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE email_log (id INT NOT NULL, subject VARCHAR(255) NOT NULL, sender VARCHAR(255) NOT NULL, receiver VARCHAR(255) NOT NULL, content TEXT NOT NULL, status VARCHAR(255) NOT NULL, sent_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, opened_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE email_template (id INT NOT NULL, type VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, content_before_buttons TEXT NOT NULL, content_after_buttons TEXT DEFAULT NULL, PRIMARY KEY(id))');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE email_attachment_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE email_log_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE email_template_id_seq CASCADE');
        $this->addSql('DROP TABLE email_attachment');
        $this->addSql('DROP TABLE email_log');
        $this->addSql('DROP TABLE email_template');
    }
}
