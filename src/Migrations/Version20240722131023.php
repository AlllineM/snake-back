<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240722131023 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE rate_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE user_rate_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE rate (id INT NOT NULL, author_id INT NOT NULL, subject_id INT NOT NULL, date_time TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_DFEC3F39F675F31B ON rate (author_id)');
        $this->addSql('CREATE INDEX IDX_DFEC3F3923EDC87 ON rate (subject_id)');
        $this->addSql('CREATE TABLE user_rate (id INT NOT NULL, nick VARCHAR(255) DEFAULT NULL, login VARCHAR(255) DEFAULT NULL, photo_link VARCHAR(255) DEFAULT NULL, token VARCHAR(255) DEFAULT NULL, reg_date_time TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE rate ADD CONSTRAINT FK_DFEC3F39F675F31B FOREIGN KEY (author_id) REFERENCES user_rate (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE rate ADD CONSTRAINT FK_DFEC3F3923EDC87 FOREIGN KEY (subject_id) REFERENCES user_rate (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE rate DROP CONSTRAINT FK_DFEC3F39F675F31B');
        $this->addSql('ALTER TABLE rate DROP CONSTRAINT FK_DFEC3F3923EDC87');
        $this->addSql('DROP SEQUENCE rate_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE user_rate_id_seq CASCADE');
        $this->addSql('DROP TABLE rate');
        $this->addSql('DROP TABLE user_rate');
    }
}
