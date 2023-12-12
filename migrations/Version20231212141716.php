<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231212141716 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE meetings (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, prof_id_id INTEGER NOT NULL, CONSTRAINT FK_44FE52E26E851E1D FOREIGN KEY (prof_id_id) REFERENCES professor (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_44FE52E26E851E1D ON meetings (prof_id_id)');
        $this->addSql('CREATE TABLE professor (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, teacher VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, total_score INTEGER NOT NULL)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE meetings');
        $this->addSql('DROP TABLE professor');
    }
}
