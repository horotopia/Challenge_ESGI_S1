<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240211081934 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE invoice DROP CONSTRAINT fk_90651744dc2902e0');
        $this->addSql('ALTER TABLE invoice DROP CONSTRAINT fk_9065174472bb1336');
        $this->addSql('DROP INDEX idx_9065174472bb1336');
        $this->addSql('DROP INDEX idx_90651744dc2902e0');
        $this->addSql('ALTER TABLE invoice ADD client_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE invoice ADD quote_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE invoice ADD invoice_number VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE invoice DROP client_id_id');
        $this->addSql('ALTER TABLE invoice DROP quote_id_id');
        $this->addSql('ALTER TABLE invoice ALTER amount TYPE DOUBLE PRECISION');
        $this->addSql('ALTER TABLE invoice ALTER updated_at DROP NOT NULL');
        $this->addSql('ALTER TABLE invoice ALTER payment_date DROP NOT NULL');
        $this->addSql('ALTER TABLE invoice ALTER user_validate DROP NOT NULL');
        $this->addSql('ALTER TABLE invoice ADD CONSTRAINT FK_9065174419EB6921 FOREIGN KEY (client_id) REFERENCES client (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE invoice ADD CONSTRAINT FK_90651744DB805178 FOREIGN KEY (quote_id) REFERENCES quote (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_9065174419EB6921 ON invoice (client_id)');
        $this->addSql('CREATE INDEX IDX_90651744DB805178 ON invoice (quote_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE invoice DROP CONSTRAINT FK_9065174419EB6921');
        $this->addSql('ALTER TABLE invoice DROP CONSTRAINT FK_90651744DB805178');
        $this->addSql('DROP INDEX IDX_9065174419EB6921');
        $this->addSql('DROP INDEX IDX_90651744DB805178');
        $this->addSql('ALTER TABLE invoice ADD client_id_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE invoice ADD quote_id_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE invoice DROP client_id');
        $this->addSql('ALTER TABLE invoice DROP quote_id');
        $this->addSql('ALTER TABLE invoice DROP invoice_number');
        $this->addSql('ALTER TABLE invoice ALTER amount TYPE NUMERIC(10, 4)');
        $this->addSql('ALTER TABLE invoice ALTER updated_at SET NOT NULL');
        $this->addSql('ALTER TABLE invoice ALTER payment_date SET NOT NULL');
        $this->addSql('ALTER TABLE invoice ALTER user_validate SET NOT NULL');
        $this->addSql('ALTER TABLE invoice ADD CONSTRAINT fk_90651744dc2902e0 FOREIGN KEY (client_id_id) REFERENCES client (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE invoice ADD CONSTRAINT fk_9065174472bb1336 FOREIGN KEY (quote_id_id) REFERENCES quote (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_9065174472bb1336 ON invoice (quote_id_id)');
        $this->addSql('CREATE INDEX idx_90651744dc2902e0 ON invoice (client_id_id)');
    }
}
