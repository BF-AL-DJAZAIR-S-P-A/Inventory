<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251009101311 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE inventaire_historique DROP FOREIGN KEY FK_27286769FB88E14F');
        $this->addSql('ALTER TABLE inventaire_historique DROP FOREIGN KEY FK_27286769CE430A85');
        $this->addSql('ALTER TABLE inventaire_historique CHANGE inventaire_id inventaire_id INT DEFAULT NULL, CHANGE utilisateur_id utilisateur_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE inventaire_historique ADD CONSTRAINT FK_27286769FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES `user` (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE inventaire_historique ADD CONSTRAINT FK_27286769CE430A85 FOREIGN KEY (inventaire_id) REFERENCES inventaire (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE inventaire_item ADD entrepot_id INT DEFAULT NULL, ADD ecart DOUBLE PRECISION DEFAULT NULL, CHANGE quantite_reelle quantite_reelle DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE inventaire_item ADD CONSTRAINT FK_D05778DB72831E97 FOREIGN KEY (entrepot_id) REFERENCES entrepot (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_D05778DB72831E97 ON inventaire_item (entrepot_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE inventaire_historique DROP FOREIGN KEY FK_27286769CE430A85');
        $this->addSql('ALTER TABLE inventaire_historique DROP FOREIGN KEY FK_27286769FB88E14F');
        $this->addSql('ALTER TABLE inventaire_historique CHANGE inventaire_id inventaire_id INT NOT NULL, CHANGE utilisateur_id utilisateur_id INT NOT NULL');
        $this->addSql('ALTER TABLE inventaire_historique ADD CONSTRAINT FK_27286769CE430A85 FOREIGN KEY (inventaire_id) REFERENCES inventaire (id)');
        $this->addSql('ALTER TABLE inventaire_historique ADD CONSTRAINT FK_27286769FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE inventaire_item DROP FOREIGN KEY FK_D05778DB72831E97');
        $this->addSql('DROP INDEX IDX_D05778DB72831E97 ON inventaire_item');
        $this->addSql('ALTER TABLE inventaire_item DROP entrepot_id, DROP ecart, CHANGE quantite_reelle quantite_reelle DOUBLE PRECISION NOT NULL');
    }
}
