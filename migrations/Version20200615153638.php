<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200615153638 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE game_achievement (game_id INT NOT NULL, achievement_id INT NOT NULL, INDEX IDX_48263DF3E48FD905 (game_id), INDEX IDX_48263DF3B3EC99FE (achievement_id), PRIMARY KEY(game_id, achievement_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE game_achievement ADD CONSTRAINT FK_48263DF3E48FD905 FOREIGN KEY (game_id) REFERENCES game (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE game_achievement ADD CONSTRAINT FK_48263DF3B3EC99FE FOREIGN KEY (achievement_id) REFERENCES achievement (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE game_achievement');
    }
}
