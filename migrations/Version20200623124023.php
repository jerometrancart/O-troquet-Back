<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200623124023 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_friends_user (user_friends_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_808487B9EEAD45DB (user_friends_id), INDEX IDX_808487B9A76ED395 (user_id), PRIMARY KEY(user_friends_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_friends_user ADD CONSTRAINT FK_808487B9EEAD45DB FOREIGN KEY (user_friends_id) REFERENCES user_friends (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_friends_user ADD CONSTRAINT FK_808487B9A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_friends DROP FOREIGN KEY FK_79E36E636A5458E8');
        $this->addSql('ALTER TABLE user_friends DROP FOREIGN KEY FK_79E36E63A76ED395');
        $this->addSql('DROP INDEX IDX_79E36E63A76ED395 ON user_friends');
        $this->addSql('DROP INDEX IDX_79E36E636A5458E8 ON user_friends');
        $this->addSql('ALTER TABLE user_friends DROP user_id, DROP friend_id');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE user_friends_user');
        $this->addSql('ALTER TABLE user_friends ADD user_id INT DEFAULT NULL, ADD friend_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user_friends ADD CONSTRAINT FK_79E36E636A5458E8 FOREIGN KEY (friend_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_friends ADD CONSTRAINT FK_79E36E63A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_79E36E63A76ED395 ON user_friends (user_id)');
        $this->addSql('CREATE INDEX IDX_79E36E636A5458E8 ON user_friends (friend_id)');
    }
}
