<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240211134723 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE category ADD company_id_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE category ADD user_created_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE category ADD user_updated_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE category ADD created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE category ADD updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE category ADD CONSTRAINT FK_64C19C138B53C32 FOREIGN KEY (company_id_id) REFERENCES company (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE category ADD CONSTRAINT FK_64C19C1F987D8A8 FOREIGN KEY (user_created_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE category ADD CONSTRAINT FK_64C19C1316B011F FOREIGN KEY (user_updated_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_64C19C138B53C32 ON category (company_id_id)');
        $this->addSql('CREATE INDEX IDX_64C19C1F987D8A8 ON category (user_created_id)');
        $this->addSql('CREATE INDEX IDX_64C19C1316B011F ON category (user_updated_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE category DROP CONSTRAINT FK_64C19C138B53C32');
        $this->addSql('ALTER TABLE category DROP CONSTRAINT FK_64C19C1F987D8A8');
        $this->addSql('ALTER TABLE category DROP CONSTRAINT FK_64C19C1316B011F');
        $this->addSql('DROP INDEX IDX_64C19C138B53C32');
        $this->addSql('DROP INDEX IDX_64C19C1F987D8A8');
        $this->addSql('DROP INDEX IDX_64C19C1316B011F');
        $this->addSql('ALTER TABLE category DROP company_id_id');
        $this->addSql('ALTER TABLE category DROP user_created_id');
        $this->addSql('ALTER TABLE category DROP user_updated_id');
        $this->addSql('ALTER TABLE category DROP created_at');
        $this->addSql('ALTER TABLE category DROP updated_at');
    }
}
