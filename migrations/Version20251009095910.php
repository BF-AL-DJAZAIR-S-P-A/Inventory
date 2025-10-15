<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251009095910 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE produit_entrepot (id INT AUTO_INCREMENT NOT NULL, produit_id INT NOT NULL, entrepot_id INT NOT NULL, quantite NUMERIC(10, 2) NOT NULL, stock_minimum NUMERIC(10, 2) DEFAULT NULL, actif TINYINT(1) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_F11A2549F347EFB (produit_id), INDEX IDX_F11A254972831E97 (entrepot_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE produit_entrepot ADD CONSTRAINT FK_F11A2549F347EFB FOREIGN KEY (produit_id) REFERENCES produit (id)');
        $this->addSql('ALTER TABLE produit_entrepot ADD CONSTRAINT FK_F11A254972831E97 FOREIGN KEY (entrepot_id) REFERENCES entrepot (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE produit_entrepot DROP FOREIGN KEY FK_F11A2549F347EFB');
        $this->addSql('ALTER TABLE produit_entrepot DROP FOREIGN KEY FK_F11A254972831E97');
        $this->addSql('DROP TABLE produit_entrepot');
    }
}
