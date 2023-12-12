<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231212123420 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__meet_eval AS SELECT id, meeting_id, score, info, creation_time FROM meet_eval');
        $this->addSql('DROP TABLE meet_eval');
        $this->addSql('CREATE TABLE meet_eval (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, meeting_id INTEGER NOT NULL, score INTEGER NOT NULL, info VARCHAR(255) DEFAULT NULL, creation_time DATETIME NOT NULL)');
        $this->addSql('INSERT INTO meet_eval (id, meeting_id, score, info, creation_time) SELECT id, meeting_id, score, info, creation_time FROM __temp__meet_eval');
        $this->addSql('DROP TABLE __temp__meet_eval');
        $this->addSql('CREATE TEMPORARY TABLE __temp__meetings AS SELECT id, prof_id, meeting_room, meeting_name, meeting_start, meeting_end, score_sum FROM meetings');
        $this->addSql('DROP TABLE meetings');
        $this->addSql('CREATE TABLE meetings (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, prof_id INTEGER NOT NULL, meeting_room VARCHAR(255) NOT NULL, meeting_name VARCHAR(255) NOT NULL, meeting_start DATETIME NOT NULL, meeting_end DATETIME NOT NULL, score_sum INTEGER DEFAULT NULL)');
        $this->addSql('INSERT INTO meetings (id, prof_id, meeting_room, meeting_name, meeting_start, meeting_end, score_sum) SELECT id, prof_id, meeting_room, meeting_name, meeting_start, meeting_end, score_sum FROM __temp__meetings');
        $this->addSql('DROP TABLE __temp__meetings');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE user');
        $this->addSql('CREATE TEMPORARY TABLE __temp__meet_eval AS SELECT id, score, info, creation_time, meeting_id FROM meet_eval');
        $this->addSql('DROP TABLE meet_eval');
        $this->addSql('CREATE TABLE meet_eval (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, meeting_id INTEGER NOT NULL, score INTEGER NOT NULL, info VARCHAR(255) DEFAULT NULL, creation_time DATETIME NOT NULL, CONSTRAINT meet_eval_meetings_id_fk FOREIGN KEY (meeting_id) REFERENCES meetings (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO meet_eval (id, score, info, creation_time, meeting_id) SELECT id, score, info, creation_time, meeting_id FROM __temp__meet_eval');
        $this->addSql('DROP TABLE __temp__meet_eval');
        $this->addSql('CREATE INDEX IDX_4436CC4B67433D9C ON meet_eval (meeting_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__meetings AS SELECT id, prof_id, meeting_room, meeting_name, meeting_start, meeting_end, score_sum FROM meetings');
        $this->addSql('DROP TABLE meetings');
        $this->addSql('CREATE TABLE meetings (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, prof_id INTEGER NOT NULL, meeting_room VARCHAR(255) NOT NULL, meeting_name VARCHAR(255) NOT NULL, meeting_start DATETIME NOT NULL, meeting_end DATETIME NOT NULL, score_sum INTEGER DEFAULT NULL, CONSTRAINT meetings_professor_id_fk FOREIGN KEY (prof_id) REFERENCES professor (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO meetings (id, prof_id, meeting_room, meeting_name, meeting_start, meeting_end, score_sum) SELECT id, prof_id, meeting_room, meeting_name, meeting_start, meeting_end, score_sum FROM __temp__meetings');
        $this->addSql('DROP TABLE __temp__meetings');
        $this->addSql('CREATE INDEX IDX_44FE52E2ABC1F7FE ON meetings (prof_id)');
    }
}
