<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231223005906 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE users DROP CONSTRAINT fk_1483a5e91a867e8f');
        $this->addSql('DROP INDEX idx_1483a5e91a867e8f');
        $this->addSql('ALTER TABLE users ADD id_entreprise INT DEFAULT NULL');
        $this->addSql('ALTER TABLE users DROP id_entreprise_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE "users" ADD id_entreprise_id INT NOT NULL');
        $this->addSql('ALTER TABLE "users" DROP id_entreprise');
        $this->addSql('ALTER TABLE "users" ADD CONSTRAINT fk_1483a5e91a867e8f FOREIGN KEY (id_entreprise_id) REFERENCES entreprise (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_1483a5e91a867e8f ON "users" (id_entreprise_id)');
    }
}
