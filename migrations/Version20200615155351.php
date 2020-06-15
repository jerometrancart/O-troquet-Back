<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200615155351 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE play DROP FOREIGN KEY FK_5E89DEBA4D77E7D8');
        $this->addSql('ALTER TABLE play DROP FOREIGN KEY FK_5E89DEBA9D86650F');
        $this->addSql('DROP INDEX IDX_5E89DEBA4D77E7D8 ON play');
        $this->addSql('DROP INDEX IDX_5E89DEBA9D86650F ON play');
        $this->addSql('ALTER TABLE play ADD game_id INT NOT NULL, ADD user_id INT NOT NULL, DROP game_id_id, DROP user_id_id');
        $this->addSql('ALTER TABLE play ADD CONSTRAINT FK_5E89DEBAE48FD905 FOREIGN KEY (game_id) REFERENCES game (id)');
        $this->addSql('ALTER TABLE play ADD CONSTRAINT FK_5E89DEBAA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_5E89DEBAE48FD905 ON play (game_id)');
        $this->addSql('CREATE INDEX IDX_5E89DEBAA76ED395 ON play (user_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE play DROP FOREIGN KEY FK_5E89DEBAE48FD905');
        $this->addSql('ALTER TABLE play DROP FOREIGN KEY FK_5E89DEBAA76ED395');
        $this->addSql('DROP INDEX IDX_5E89DEBAE48FD905 ON play');
        $this->addSql('DROP INDEX IDX_5E89DEBAA76ED395 ON play');
        $this->addSql('ALTER TABLE play ADD game_id_id INT NOT NULL, ADD user_id_id INT NOT NULL, DROP game_id, DROP user_id');
        $this->addSql('ALTER TABLE play ADD CONSTRAINT FK_5E89DEBA4D77E7D8 FOREIGN KEY (game_id_id) REFERENCES game (id)');
        $this->addSql('ALTER TABLE play ADD CONSTRAINT FK_5E89DEBA9D86650F FOREIGN KEY (user_id_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_5E89DEBA4D77E7D8 ON play (game_id_id)');
        $this->addSql('CREATE INDEX IDX_5E89DEBA9D86650F ON play (user_id_id)');
    }
}
