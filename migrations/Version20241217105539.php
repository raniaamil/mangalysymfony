<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241217105539 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commentaire CHANGE critiques_id critiques_id INT NOT NULL');
        $this->addSql('ALTER TABLE commentaire RENAME INDEX idx_67f068bc1ebaf6cc TO IDX_67F068BC6DE639D6');
        $this->addSql('ALTER TABLE critiques ADD CONSTRAINT FK_2712BED9A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_2712BED9A76ED395 ON critiques (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commentaire CHANGE critiques_id critiques_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE commentaire RENAME INDEX idx_67f068bc6de639d6 TO IDX_67F068BC1EBAF6CC');
        $this->addSql('ALTER TABLE critiques DROP FOREIGN KEY FK_2712BED9A76ED395');
        $this->addSql('DROP INDEX IDX_2712BED9A76ED395 ON critiques');
    }
}
