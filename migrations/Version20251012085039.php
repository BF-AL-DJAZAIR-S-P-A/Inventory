<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251012085039 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE produit_entrepot ADD utilisateur_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE produit_entrepot ADD CONSTRAINT FK_F11A2549FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES `user` (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_F11A2549FB88E14F ON produit_entrepot (utilisateur_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE produit_entrepot DROP FOREIGN KEY FK_F11A2549FB88E14F');
        $this->addSql('DROP INDEX IDX_F11A2549FB88E14F ON produit_entrepot');
        $this->addSql('ALTER TABLE produit_entrepot DROP utilisateur_id');
    }
}
