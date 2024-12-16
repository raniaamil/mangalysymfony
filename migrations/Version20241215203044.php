<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241215203044 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE critiques ADD user_id INT NOT NULL');
        $this->addSql('ALTER TABLE critiques ADD CONSTRAINT FK_BFDD3168A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_BFDD3168A76ED395 ON critiques (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE critiques DROP FOREIGN KEY FK_BFDD3168A76ED395');
        $this->addSql('DROP INDEX IDX_BFDD3168A76ED395 ON critiques');
        $this->addSql('ALTER TABLE critiques DROP user_id');
    }
}
