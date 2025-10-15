<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251012090250 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE inventaire_item ADD produit_entrepot_id INT DEFAULT NULL, ADD saisi_par_id INT DEFAULT NULL, ADD created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE inventaire_item ADD CONSTRAINT FK_D05778DBC9B5A13 FOREIGN KEY (produit_entrepot_id) REFERENCES produit_entrepot (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE inventaire_item ADD CONSTRAINT FK_D05778DBB0F809FE FOREIGN KEY (saisi_par_id) REFERENCES `user` (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_D05778DBC9B5A13 ON inventaire_item (produit_entrepot_id)');
        $this->addSql('CREATE INDEX IDX_D05778DBB0F809FE ON inventaire_item (saisi_par_id)');
        $this->addSql('ALTER TABLE produit_entrepot ADD deleted_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE inventaire_item DROP FOREIGN KEY FK_D05778DBC9B5A13');
        $this->addSql('ALTER TABLE inventaire_item DROP FOREIGN KEY FK_D05778DBB0F809FE');
        $this->addSql('DROP INDEX IDX_D05778DBC9B5A13 ON inventaire_item');
        $this->addSql('DROP INDEX IDX_D05778DBB0F809FE ON inventaire_item');
        $this->addSql('ALTER TABLE inventaire_item DROP produit_entrepot_id, DROP saisi_par_id, DROP created_at, DROP updated_at');
        $this->addSql('ALTER TABLE produit_entrepot DROP deleted_at');
    }
}
