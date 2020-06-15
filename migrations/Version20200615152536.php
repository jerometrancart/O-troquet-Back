<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200615152536 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE achievement_user (achievement_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_E71294E0B3EC99FE (achievement_id), INDEX IDX_E71294E0A76ED395 (user_id), PRIMARY KEY(achievement_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE achievement_user ADD CONSTRAINT FK_E71294E0B3EC99FE FOREIGN KEY (achievement_id) REFERENCES achievement (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE achievement_user ADD CONSTRAINT FK_E71294E0A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE achievement_user');
    }
}
