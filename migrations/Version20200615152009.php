<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200615152009 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE achievement (id INT AUTO_INCREMENT NOT NULL, phrase VARCHAR(64) NOT NULL, icon VARCHAR(64) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE game (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(64) NOT NULL, icon VARCHAR(64) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE play (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, game_id INT NOT NULL, win TINYINT(1) NOT NULL, date DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(64) NOT NULL, password VARCHAR(64) NOT NULL, username VARCHAR(64) NOT NULL, avatar VARCHAR(64) DEFAULT NULL, role VARCHAR(64) NOT NULL, is_active TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_friends (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, friend_id INT DEFAULT NULL, is_requested TINYINT(1) NOT NULL, is_accepted TINYINT(1) NOT NULL, INDEX IDX_79E36E63A76ED395 (user_id), INDEX IDX_79E36E636A5458E8 (friend_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_friends ADD CONSTRAINT FK_79E36E63A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_friends ADD CONSTRAINT FK_79E36E636A5458E8 FOREIGN KEY (friend_id) REFERENCES user (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_friends DROP FOREIGN KEY FK_79E36E63A76ED395');
        $this->addSql('ALTER TABLE user_friends DROP FOREIGN KEY FK_79E36E636A5458E8');
        $this->addSql('DROP TABLE achievement');
        $this->addSql('DROP TABLE game');
        $this->addSql('DROP TABLE play');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE user_friends');
    }
}
