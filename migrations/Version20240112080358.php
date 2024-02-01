<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240112080358 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE devis ADD num_devis VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE devis ADD total_ht DOUBLE PRECISION NOT NULL');
        $this->addSql('ALTER TABLE devis ADD total_ttc DOUBLE PRECISION NOT NULL');
        $this->addSql('ALTER TABLE devis DROP ht_ttc');
        $this->addSql('ALTER TABLE devis DROP total');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE devis ADD ht_ttc BOOLEAN NOT NULL');
        $this->addSql('ALTER TABLE devis ADD total NUMERIC(10, 4) NOT NULL');
        $this->addSql('ALTER TABLE devis DROP num_devis');
        $this->addSql('ALTER TABLE devis DROP total_ht');
        $this->addSql('ALTER TABLE devis DROP total_ttc');
    }
}
