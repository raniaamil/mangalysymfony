<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241231222420 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commentaire ADD theorie_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE commentaire ADD CONSTRAINT FK_67F068BC59565E1E FOREIGN KEY (theorie_id) REFERENCES theorie (id)');
        $this->addSql('CREATE INDEX IDX_67F068BC59565E1E ON commentaire (theorie_id)');
        $this->addSql('ALTER TABLE commentaire RENAME INDEX idx_67f068bc1ebaf6cc TO IDX_67F068BC6DE639D6');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commentaire DROP FOREIGN KEY FK_67F068BC59565E1E');
        $this->addSql('DROP INDEX IDX_67F068BC59565E1E ON commentaire');
        $this->addSql('ALTER TABLE commentaire DROP theorie_id');
        $this->addSql('ALTER TABLE commentaire RENAME INDEX idx_67f068bc6de639d6 TO IDX_67F068BC1EBAF6CC');
    }
}
