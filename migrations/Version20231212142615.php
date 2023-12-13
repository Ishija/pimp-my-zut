<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231212142615 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE meet_eval (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, meeting_id INTEGER NOT NULL, score INTEGER NOT NULL, info VARCHAR(255) DEFAULT NULL, creation_time DATETIME NOT NULL, CONSTRAINT FK_4436CC4B67433D9C FOREIGN KEY (meeting_id) REFERENCES meetings (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_4436CC4B67433D9C ON meet_eval (meeting_id)');
        $this->addSql('CREATE TABLE meetings (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, prof_id INTEGER NOT NULL, meeting_room VARCHAR(255) NOT NULL, meeting_name VARCHAR(255) NOT NULL, meeting_start DATETIME NOT NULL, meeting_end DATETIME NOT NULL, score_sum INTEGER DEFAULT NULL, CONSTRAINT FK_44FE52E2ABC1F7FE FOREIGN KEY (prof_id) REFERENCES professor (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_44FE52E2ABC1F7FE ON meetings (prof_id)');
        $this->addSql('CREATE TABLE professor (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, teacher VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, total_score INTEGER NOT NULL)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE meet_eval');
        $this->addSql('DROP TABLE meetings');
        $this->addSql('DROP TABLE professor');
    }
}
