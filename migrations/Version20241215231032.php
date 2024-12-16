<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241215231032 extends AbstractMigration
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
        $this->addSql('ALTER TABLE critiques ADD date_publication DATETIME NOT NULL, DROP user_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commentaire CHANGE critiques_id critiques_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE commentaire RENAME INDEX idx_67f068bc6de639d6 TO IDX_67F068BC1EBAF6CC');
        $this->addSql('ALTER TABLE critiques ADD user_id INT NOT NULL, DROP date_publication');
    }
}
