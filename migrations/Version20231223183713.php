<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231223183713 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE entreprise ALTER email DROP NOT NULL');
        $this->addSql('ALTER TABLE entreprise ALTER description DROP NOT NULL');
        $this->addSql('ALTER TABLE entreprise ALTER telephone DROP NOT NULL');
        $this->addSql('ALTER TABLE entreprise ALTER adresse DROP NOT NULL');
        $this->addSql('ALTER TABLE entreprise ALTER code_postal DROP NOT NULL');
        $this->addSql('ALTER TABLE entreprise ALTER pays DROP NOT NULL');
        $this->addSql('ALTER TABLE entreprise ALTER compte_bancaire DROP NOT NULL');
        $this->addSql('ALTER TABLE entreprise ALTER logo DROP NOT NULL');
        $this->addSql('ALTER TABLE entreprise ALTER abonnement DROP NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE entreprise ALTER email SET NOT NULL');
        $this->addSql('ALTER TABLE entreprise ALTER description SET NOT NULL');
        $this->addSql('ALTER TABLE entreprise ALTER telephone SET NOT NULL');
        $this->addSql('ALTER TABLE entreprise ALTER adresse SET NOT NULL');
        $this->addSql('ALTER TABLE entreprise ALTER code_postal SET NOT NULL');
        $this->addSql('ALTER TABLE entreprise ALTER pays SET NOT NULL');
        $this->addSql('ALTER TABLE entreprise ALTER compte_bancaire SET NOT NULL');
        $this->addSql('ALTER TABLE entreprise ALTER logo SET NOT NULL');
        $this->addSql('ALTER TABLE entreprise ALTER abonnement SET NOT NULL');
    }
}
