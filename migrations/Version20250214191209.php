<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Migration pour supprimer la relation entre Critiques et Commentaire.
 */
final class Version20250214191209 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Supprime la relation entre Critiques et Commentaire (suppression de la colonne critiques_id dans la table commentaire).';
    }

    public function up(Schema $schema): void
    {
        // Supprimer la contrainte étrangère liant commentaire à critiques
        $this->addSql('ALTER TABLE commentaire DROP FOREIGN KEY FK_67F068BC1EBAF6CC');
        // Supprimer l'index associé
        $this->addSql('DROP INDEX IDX_67F068BC6DE639D6 ON commentaire');
        // Supprimer la colonne critiques_id
        $this->addSql('ALTER TABLE commentaire DROP critiques_id');
    }

    public function down(Schema $schema): void
    {
        // Restaurer la colonne critiques_id
        $this->addSql('ALTER TABLE commentaire ADD critiques_id INT DEFAULT NULL');
        // Restaurer l'index sur critiques_id
        $this->addSql('CREATE INDEX IDX_67F068BC6DE639D6 ON commentaire (critiques_id)');
        // Restaurer la contrainte étrangère vers critiques
        $this->addSql('ALTER TABLE commentaire ADD CONSTRAINT FK_67F068BC1EBAF6CC FOREIGN KEY (critiques_id) REFERENCES critiques (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
    }
}
