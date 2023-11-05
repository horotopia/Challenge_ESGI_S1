<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231101114249 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE categorie_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE client_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE devis_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE entreprise_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE facture_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE produit_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE users_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE categorie (id INT NOT NULL, nom VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE client (id INT NOT NULL, id_entreprise_id INT DEFAULT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, adresse VARCHAR(255) NOT NULL, ville VARCHAR(255) NOT NULL, code_postal INT NOT NULL, telephone INT NOT NULL, create_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, update_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, user_create VARCHAR(255) NOT NULL, user_update VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_C74404551A867E8F ON client (id_entreprise_id)');
        $this->addSql('COMMENT ON COLUMN client.create_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN client.update_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE devis (id INT NOT NULL, id_client_id INT DEFAULT NULL, statut VARCHAR(255) NOT NULL, ht_ttc BOOLEAN NOT NULL, total NUMERIC(10, 4) NOT NULL, tranches INT NOT NULL, create_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, update_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, date_echeance TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, user_create VARCHAR(255) NOT NULL, user_update VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_8B27C52B99DED506 ON devis (id_client_id)');
        $this->addSql('COMMENT ON COLUMN devis.create_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN devis.update_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE devis_produit (devis_id INT NOT NULL, produit_id INT NOT NULL, PRIMARY KEY(devis_id, produit_id))');
        $this->addSql('CREATE INDEX IDX_BB4B777B41DEFADA ON devis_produit (devis_id)');
        $this->addSql('CREATE INDEX IDX_BB4B777BF347EFB ON devis_produit (produit_id)');
        $this->addSql('CREATE TABLE entreprise (id INT NOT NULL, nom VARCHAR(255) NOT NULL, siret INT NOT NULL, email VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, site_internet VARCHAR(255) DEFAULT NULL, telephone INT NOT NULL, adresse VARCHAR(255) NOT NULL, code_postal INT NOT NULL, pays VARCHAR(255) NOT NULL, compte_bancaire VARCHAR(255) NOT NULL, logo VARCHAR(255) NOT NULL, create_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, abonnement VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN entreprise.create_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE facture (id INT NOT NULL, id_client_id INT DEFAULT NULL, id_devis_id INT DEFAULT NULL, statut VARCHAR(255) NOT NULL, montant NUMERIC(10, 4) NOT NULL, methode_paiement VARCHAR(255) NOT NULL, create_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, update_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, date_echeance TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, date_paiement TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, user_validate VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_FE86641099DED506 ON facture (id_client_id)');
        $this->addSql('CREATE INDEX IDX_FE8664101105164F ON facture (id_devis_id)');
        $this->addSql('COMMENT ON COLUMN facture.create_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN facture.update_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE produit (id INT NOT NULL, id_categorie_id INT DEFAULT NULL, id_entreprise_id INT DEFAULT NULL, nom VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, marque VARCHAR(255) NOT NULL, prix_unitaire NUMERIC(10, 4) NOT NULL, tva INT NOT NULL, quantite_disponible INT NOT NULL, create_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, update_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, user_create VARCHAR(255) NOT NULL, user_update VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_29A5EC279F34925F ON produit (id_categorie_id)');
        $this->addSql('CREATE INDEX IDX_29A5EC271A867E8F ON produit (id_entreprise_id)');
        $this->addSql('COMMENT ON COLUMN produit.create_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN produit.update_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE users (id INT NOT NULL, id_entreprise_id INT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, is_verified BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9E7927C74 ON users (email)');
        $this->addSql('CREATE INDEX IDX_1483A5E91A867E8F ON users (id_entreprise_id)');
        $this->addSql('CREATE TABLE messenger_messages (id BIGSERIAL NOT NULL, body TEXT NOT NULL, headers TEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, available_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, delivered_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
        $this->addSql('COMMENT ON COLUMN messenger_messages.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messenger_messages.available_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messenger_messages.delivered_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE OR REPLACE FUNCTION notify_messenger_messages() RETURNS TRIGGER AS $$
            BEGIN
                PERFORM pg_notify(\'messenger_messages\', NEW.queue_name::text);
                RETURN NEW;
            END;
        $$ LANGUAGE plpgsql;');
        $this->addSql('DROP TRIGGER IF EXISTS notify_trigger ON messenger_messages;');
        $this->addSql('CREATE TRIGGER notify_trigger AFTER INSERT OR UPDATE ON messenger_messages FOR EACH ROW EXECUTE PROCEDURE notify_messenger_messages();');
        $this->addSql('ALTER TABLE client ADD CONSTRAINT FK_C74404551A867E8F FOREIGN KEY (id_entreprise_id) REFERENCES entreprise (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE devis ADD CONSTRAINT FK_8B27C52B99DED506 FOREIGN KEY (id_client_id) REFERENCES client (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE devis_produit ADD CONSTRAINT FK_BB4B777B41DEFADA FOREIGN KEY (devis_id) REFERENCES devis (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE devis_produit ADD CONSTRAINT FK_BB4B777BF347EFB FOREIGN KEY (produit_id) REFERENCES produit (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE facture ADD CONSTRAINT FK_FE86641099DED506 FOREIGN KEY (id_client_id) REFERENCES client (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE facture ADD CONSTRAINT FK_FE8664101105164F FOREIGN KEY (id_devis_id) REFERENCES devis (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE produit ADD CONSTRAINT FK_29A5EC279F34925F FOREIGN KEY (id_categorie_id) REFERENCES categorie (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE produit ADD CONSTRAINT FK_29A5EC271A867E8F FOREIGN KEY (id_entreprise_id) REFERENCES entreprise (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT FK_1483A5E91A867E8F FOREIGN KEY (id_entreprise_id) REFERENCES entreprise (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE categorie_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE client_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE devis_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE entreprise_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE facture_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE produit_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE users_id_seq CASCADE');
        $this->addSql('ALTER TABLE client DROP CONSTRAINT FK_C74404551A867E8F');
        $this->addSql('ALTER TABLE devis DROP CONSTRAINT FK_8B27C52B99DED506');
        $this->addSql('ALTER TABLE devis_produit DROP CONSTRAINT FK_BB4B777B41DEFADA');
        $this->addSql('ALTER TABLE devis_produit DROP CONSTRAINT FK_BB4B777BF347EFB');
        $this->addSql('ALTER TABLE facture DROP CONSTRAINT FK_FE86641099DED506');
        $this->addSql('ALTER TABLE facture DROP CONSTRAINT FK_FE8664101105164F');
        $this->addSql('ALTER TABLE produit DROP CONSTRAINT FK_29A5EC279F34925F');
        $this->addSql('ALTER TABLE produit DROP CONSTRAINT FK_29A5EC271A867E8F');
        $this->addSql('ALTER TABLE users DROP CONSTRAINT FK_1483A5E91A867E8F');
        $this->addSql('DROP TABLE categorie');
        $this->addSql('DROP TABLE client');
        $this->addSql('DROP TABLE devis');
        $this->addSql('DROP TABLE devis_produit');
        $this->addSql('DROP TABLE entreprise');
        $this->addSql('DROP TABLE facture');
        $this->addSql('DROP TABLE produit');
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
