<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200615153957 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE play_game (play_id INT NOT NULL, game_id INT NOT NULL, INDEX IDX_282ECF5D25576DBD (play_id), INDEX IDX_282ECF5DE48FD905 (game_id), PRIMARY KEY(play_id, game_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE play_user (play_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_8696289825576DBD (play_id), INDEX IDX_86962898A76ED395 (user_id), PRIMARY KEY(play_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE play_game ADD CONSTRAINT FK_282ECF5D25576DBD FOREIGN KEY (play_id) REFERENCES play (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE play_game ADD CONSTRAINT FK_282ECF5DE48FD905 FOREIGN KEY (game_id) REFERENCES game (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE play_user ADD CONSTRAINT FK_8696289825576DBD FOREIGN KEY (play_id) REFERENCES play (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE play_user ADD CONSTRAINT FK_86962898A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE play DROP user_id, DROP game_id');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE play_game');
        $this->addSql('DROP TABLE play_user');
        $this->addSql('ALTER TABLE play ADD user_id INT NOT NULL, ADD game_id INT NOT NULL');
    }
}
