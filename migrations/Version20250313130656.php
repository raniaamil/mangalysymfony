<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250313130656 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE INDEX idx_commentaire_date_publication ON commentaire (date_publication)');
        $this->addSql('CREATE INDEX idx_commentaire_date_modification ON commentaire (date_modification)');
        $this->addSql('CREATE INDEX idx_commentaire_report ON commentaire (report)');
        $this->addSql('CREATE INDEX idx_critiques_titre ON critiques (titre)');
        $this->addSql('CREATE INDEX idx_critiques_date_publication ON critiques (date_publication)');
        $this->addSql('CREATE INDEX idx_critiques_date_modification ON critiques (date_modification)');
        $this->addSql('CREATE INDEX idx_critiques_report ON critiques (report)');
        $this->addSql('CREATE INDEX idx_manga_titre ON manga (titre)');
        $this->addSql('CREATE INDEX idx_manga_date_sortie ON manga (date_sortie)');
        $this->addSql('CREATE INDEX idx_post_titre ON post (titre)');
        $this->addSql('CREATE INDEX idx_post_date_publication ON post (date_publication)');
        $this->addSql('CREATE INDEX idx_post_date_modification ON post (date_modification)');
        $this->addSql('CREATE INDEX idx_post_report ON post (report)');
        $this->addSql('CREATE INDEX idx_theorie_titre ON theorie (titre)');
        $this->addSql('CREATE INDEX idx_theorie_date_publication ON theorie (date_publication)');
        $this->addSql('CREATE INDEX idx_theorie_date_modification ON theorie (date_modification)');
        $this->addSql('CREATE INDEX idx_theorie_report ON theorie (report)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX idx_commentaire_date_publication ON commentaire');
        $this->addSql('DROP INDEX idx_commentaire_date_modification ON commentaire');
        $this->addSql('DROP INDEX idx_commentaire_report ON commentaire');
        $this->addSql('DROP INDEX idx_critiques_titre ON critiques');
        $this->addSql('DROP INDEX idx_critiques_date_publication ON critiques');
        $this->addSql('DROP INDEX idx_critiques_date_modification ON critiques');
        $this->addSql('DROP INDEX idx_critiques_report ON critiques');
        $this->addSql('DROP INDEX idx_post_titre ON post');
        $this->addSql('DROP INDEX idx_post_date_publication ON post');
        $this->addSql('DROP INDEX idx_post_date_modification ON post');
        $this->addSql('DROP INDEX idx_post_report ON post');
        $this->addSql('DROP INDEX idx_theorie_titre ON theorie');
        $this->addSql('DROP INDEX idx_theorie_date_publication ON theorie');
        $this->addSql('DROP INDEX idx_theorie_date_modification ON theorie');
        $this->addSql('DROP INDEX idx_theorie_report ON theorie');
        $this->addSql('DROP INDEX idx_manga_titre ON manga');
        $this->addSql('DROP INDEX idx_manga_date_sortie ON manga');
    }
}
