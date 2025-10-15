<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251012091201 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE inventaire ADD entrepot_id INT NOT NULL');
        $this->addSql('ALTER TABLE inventaire ADD CONSTRAINT FK_338920E072831E97 FOREIGN KEY (entrepot_id) REFERENCES entrepot (id)');
        $this->addSql('CREATE INDEX IDX_338920E072831E97 ON inventaire (entrepot_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE inventaire DROP FOREIGN KEY FK_338920E072831E97');
        $this->addSql('DROP INDEX IDX_338920E072831E97 ON inventaire');
        $this->addSql('ALTER TABLE inventaire DROP entrepot_id');
    }
}
