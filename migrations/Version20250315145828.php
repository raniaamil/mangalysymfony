<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250315145828 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Modifie la colonne theorie_id pour autoriser NULL et ajuste les clés étrangères';
    }

    public function up(Schema $schema): void
    {
        // Maintenant que la base de test a été supprimée, on peut simplifier la migration
        // Cependant, on utilise toujours un try/catch pour éviter les erreurs
        
        // Commentaire
        
        // Modification pour autoriser NULL dans theorie_id
        $this->addSql('ALTER TABLE commentaire CHANGE theorie_id theorie_id INT DEFAULT NULL');
        
        // Critiques
        try {
            $this->addSql('ALTER TABLE critiques DROP FOREIGN KEY FK_2712BED97B6461');
        } catch (\Exception $e) {
            $this->write('La contrainte FK_2712BED97B6461 n\'existe pas, on continue');
        }
        $this->addSql('ALTER TABLE critiques ADD CONSTRAINT FK_2712BED97B6461 FOREIGN KEY (manga_id) REFERENCES manga (id) ON DELETE CASCADE');

        // Like
        try {
            $this->addSql('ALTER TABLE `like` DROP FOREIGN KEY FK_AC6340B34B89032C');
        } catch (\Exception $e) {
            $this->write('La contrainte FK_AC6340B34B89032C n\'existe pas, on continue');
        }
        
        try {
            $this->addSql('ALTER TABLE `like` DROP FOREIGN KEY FK_AC6340B359565E1E');
        } catch (\Exception $e) {
            $this->write('La contrainte FK_AC6340B359565E1E n\'existe pas, on continue');
        }
        
        try {
            $this->addSql('ALTER TABLE `like` DROP FOREIGN KEY FK_AC6340B36DE639D6');
        } catch (\Exception $e) {
            $this->write('La contrainte FK_AC6340B36DE639D6 n\'existe pas, on continue');
        }
        
        try {
            $this->addSql('ALTER TABLE `like` DROP FOREIGN KEY FK_AC6340B3BA9CD190');
        } catch (\Exception $e) {
            $this->write('La contrainte FK_AC6340B3BA9CD190 n\'existe pas, on continue');
        }
        
        $this->addSql('ALTER TABLE `like` ADD CONSTRAINT FK_AC6340B34B89032C FOREIGN KEY (post_id) REFERENCES post (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE `like` ADD CONSTRAINT FK_AC6340B359565E1E FOREIGN KEY (theorie_id) REFERENCES theorie (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE `like` ADD CONSTRAINT FK_AC6340B36DE639D6 FOREIGN KEY (critiques_id) REFERENCES critiques (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE `like` ADD CONSTRAINT FK_AC6340B3BA9CD190 FOREIGN KEY (commentaire_id) REFERENCES commentaire (id) ON DELETE CASCADE');

        // Manga
        try {
            $this->addSql('ALTER TABLE manga DROP FOREIGN KEY FK_765A9E034296D31F');
        } catch (\Exception $e) {
            $this->write('La contrainte FK_765A9E034296D31F n\'existe pas, on continue');
        }
        $this->addSql('ALTER TABLE manga ADD CONSTRAINT FK_765A9E034296D31F FOREIGN KEY (genre_id) REFERENCES genre (id) ON DELETE CASCADE');
        
        // Post
        try {
            $this->addSql('ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8D7B6461');
        } catch (\Exception $e) {
            $this->write('La contrainte FK_5A8A6C8D7B6461 n\'existe pas, on continue');
        }
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8D7B6461 FOREIGN KEY (manga_id) REFERENCES manga (id) ON DELETE CASCADE');
        
        // Theorie
        try {
            $this->addSql('ALTER TABLE theorie DROP FOREIGN KEY FK_8DE9401E7B6461');
        } catch (\Exception $e) {
            $this->write('La contrainte FK_8DE9401E7B6461 n\'existe pas, on continue');
        }
        $this->addSql('ALTER TABLE theorie ADD CONSTRAINT FK_8DE9401E7B6461 FOREIGN KEY (manga_id) REFERENCES manga (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // Version down simplifiée avec try/catch
        try {
            $this->addSql('ALTER TABLE commentaire CHANGE theorie_id theorie_id INT DEFAULT NULL');
            
            $this->addSql('ALTER TABLE critiques DROP FOREIGN KEY FK_2712BED97B6461');
            $this->addSql('ALTER TABLE critiques ADD CONSTRAINT FK_2712BED97B6461 FOREIGN KEY (manga_id) REFERENCES manga (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
            
            $this->addSql('ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8D7B6461');
            $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8D7B6461 FOREIGN KEY (manga_id) REFERENCES manga (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
            
            $this->addSql('ALTER TABLE theorie DROP FOREIGN KEY FK_8DE9401E7B6461');
            $this->addSql('ALTER TABLE theorie ADD CONSTRAINT FK_8DE9401E7B6461 FOREIGN KEY (manga_id) REFERENCES manga (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
            
            $this->addSql('ALTER TABLE `like` DROP FOREIGN KEY FK_AC6340B3BA9CD190');
            $this->addSql('ALTER TABLE `like` DROP FOREIGN KEY FK_AC6340B34B89032C');
            $this->addSql('ALTER TABLE `like` DROP FOREIGN KEY FK_AC6340B36DE639D6');
            $this->addSql('ALTER TABLE `like` ADD CONSTRAINT FK_AC6340B3BA9CD190 FOREIGN KEY (commentaire_id) REFERENCES commentaire (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
            $this->addSql('ALTER TABLE `like` ADD CONSTRAINT FK_AC6340B34B89032C FOREIGN KEY (post_id) REFERENCES post (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
            $this->addSql('ALTER TABLE `like` ADD CONSTRAINT FK_AC6340B36DE639D6 FOREIGN KEY (critiques_id) REFERENCES critiques (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
            
            $this->addSql('ALTER TABLE manga DROP FOREIGN KEY FK_765A9E034296D31F');
            $this->addSql('ALTER TABLE manga ADD CONSTRAINT FK_765A9E034296D31F FOREIGN KEY (genre_id) REFERENCES genre (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        } catch (\Exception $e) {
            $this->write('Une erreur s\'est produite lors du rollback: ' . $e->getMessage());
        }
    }
}