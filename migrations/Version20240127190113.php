<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240127190113 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE devis_produit_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('ALTER TABLE devis_produit DROP CONSTRAINT FK_BB4B777B41DEFADA');
        $this->addSql('ALTER TABLE devis_produit DROP CONSTRAINT FK_BB4B777BF347EFB');
        $this->addSql('ALTER TABLE devis_produit DROP CONSTRAINT devis_produit_pkey');
        $this->addSql('ALTER TABLE devis_produit ADD id INT NOT NULL');
        $this->addSql('ALTER TABLE devis_produit ADD quantite INT NOT NULL');
        $this->addSql('ALTER TABLE devis_produit ALTER devis_id DROP NOT NULL');
        $this->addSql('ALTER TABLE devis_produit ALTER produit_id DROP NOT NULL');
        $this->addSql('ALTER TABLE devis_produit ADD CONSTRAINT FK_BB4B777B41DEFADA FOREIGN KEY (devis_id) REFERENCES devis (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE devis_produit ADD CONSTRAINT FK_BB4B777BF347EFB FOREIGN KEY (produit_id) REFERENCES produit (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE devis_produit ADD PRIMARY KEY (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE devis_produit_id_seq CASCADE');
        $this->addSql('ALTER TABLE devis_produit DROP CONSTRAINT fk_bb4b777b41defada');
        $this->addSql('ALTER TABLE devis_produit DROP CONSTRAINT fk_bb4b777bf347efb');
        $this->addSql('DROP INDEX devis_produit_pkey');
        $this->addSql('ALTER TABLE devis_produit DROP id');
        $this->addSql('ALTER TABLE devis_produit DROP quantite');
        $this->addSql('ALTER TABLE devis_produit ALTER devis_id SET NOT NULL');
        $this->addSql('ALTER TABLE devis_produit ALTER produit_id SET NOT NULL');
        $this->addSql('ALTER TABLE devis_produit ADD CONSTRAINT fk_bb4b777b41defada FOREIGN KEY (devis_id) REFERENCES devis (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE devis_produit ADD CONSTRAINT fk_bb4b777bf347efb FOREIGN KEY (produit_id) REFERENCES produit (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE devis_produit ADD PRIMARY KEY (devis_id, produit_id)');
    }
}
