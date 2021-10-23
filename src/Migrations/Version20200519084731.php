<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200519084731 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE user_aliker_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE reaction (id INT NOT NULL, fact_id INT NOT NULL, user_aliker_id INT NOT NULL, date_time TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, status INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_A4D707F7156D5C2A ON reaction (fact_id)');
        $this->addSql('CREATE INDEX IDX_A4D707F75F4CC3FE ON reaction (user_aliker_id)');
        $this->addSql('CREATE TABLE user_aliker (id INT NOT NULL, phone VARCHAR(180) NOT NULL, roles TEXT NOT NULL, password VARCHAR(255) NOT NULL, nick VARCHAR(255) DEFAULT NULL, balance INT NOT NULL, rating INT NOT NULL, email VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_EFD7EF97444F97DD ON user_aliker (phone)');
        $this->addSql('COMMENT ON COLUMN user_aliker.roles IS \'(DC2Type:json)\'');
        $this->addSql('CREATE TABLE fact (id INT NOT NULL, author_id INT NOT NULL, body TEXT NOT NULL, date_time TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_6FA45B95F675F31B ON fact (author_id)');
        $this->addSql('ALTER TABLE reaction ADD CONSTRAINT FK_A4D707F7156D5C2A FOREIGN KEY (fact_id) REFERENCES fact (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE reaction ADD CONSTRAINT FK_A4D707F75F4CC3FE FOREIGN KEY (user_aliker_id) REFERENCES user_aliker (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE fact ADD CONSTRAINT FK_6FA45B95F675F31B FOREIGN KEY (author_id) REFERENCES user_aliker (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE reaction DROP CONSTRAINT FK_A4D707F75F4CC3FE');
        $this->addSql('ALTER TABLE fact DROP CONSTRAINT FK_6FA45B95F675F31B');
        $this->addSql('ALTER TABLE reaction DROP CONSTRAINT FK_A4D707F7156D5C2A');
        $this->addSql('DROP SEQUENCE user_aliker_id_seq CASCADE');
        $this->addSql('CREATE SEQUENCE user_mate_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('DROP TABLE reaction');
        $this->addSql('DROP TABLE user_aliker');
        $this->addSql('DROP TABLE fact');
    }
}
