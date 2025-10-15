<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251008120914 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE inventaire_historique (id INT AUTO_INCREMENT NOT NULL, inventaire_id INT NOT NULL, utilisateur_id INT NOT NULL, action VARCHAR(255) NOT NULL, message LONGTEXT DEFAULT NULL, date_action DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_27286769CE430A85 (inventaire_id), INDEX IDX_27286769FB88E14F (utilisateur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE inventaire_historique ADD CONSTRAINT FK_27286769CE430A85 FOREIGN KEY (inventaire_id) REFERENCES inventaire (id)');
        $this->addSql('ALTER TABLE inventaire_historique ADD CONSTRAINT FK_27286769FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES `user` (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE inventaire_historique DROP FOREIGN KEY FK_27286769CE430A85');
        $this->addSql('ALTER TABLE inventaire_historique DROP FOREIGN KEY FK_27286769FB88E14F');
        $this->addSql('DROP TABLE inventaire_historique');
    }
}
