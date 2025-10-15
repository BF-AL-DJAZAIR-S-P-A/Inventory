<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251002090446 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE inventaire (id INT AUTO_INCREMENT NOT NULL, date_debut DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', date_fin DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', statut VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE inventaire_item (id INT AUTO_INCREMENT NOT NULL, inventaire_id INT NOT NULL, produit_id INT NOT NULL, quantite_theorique DOUBLE PRECISION NOT NULL, quantite_reelle DOUBLE PRECISION NOT NULL, INDEX IDX_D05778DBCE430A85 (inventaire_id), INDEX IDX_D05778DBF347EFB (produit_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE inventaire_item ADD CONSTRAINT FK_D05778DBCE430A85 FOREIGN KEY (inventaire_id) REFERENCES inventaire (id)');
        $this->addSql('ALTER TABLE inventaire_item ADD CONSTRAINT FK_D05778DBF347EFB FOREIGN KEY (produit_id) REFERENCES produit (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE inventaire_item DROP FOREIGN KEY FK_D05778DBCE430A85');
        $this->addSql('ALTER TABLE inventaire_item DROP FOREIGN KEY FK_D05778DBF347EFB');
        $this->addSql('DROP TABLE inventaire');
        $this->addSql('DROP TABLE inventaire_item');
    }
}
