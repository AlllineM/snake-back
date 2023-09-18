<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230918150939 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE log_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE utm_user_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE log (id INT NOT NULL, utm_user_id INT DEFAULT NULL, user_aliker_id INT DEFAULT NULL, action VARCHAR(255) DEFAULT NULL, date_time TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_8F3F68C578E1CD ON log (utm_user_id)');
        $this->addSql('CREATE INDEX IDX_8F3F68C55F4CC3FE ON log (user_aliker_id)');
        $this->addSql('CREATE TABLE utm_user (id INT NOT NULL, user_aliker_id INT DEFAULT NULL, utm_source VARCHAR(255) DEFAULT NULL, utm_medium VARCHAR(255) DEFAULT NULL, utm_campaign VARCHAR(255) DEFAULT NULL, utm_term VARCHAR(255) DEFAULT NULL, utm_content VARCHAR(255) DEFAULT NULL, create_date_time TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_3B4BF96E5F4CC3FE ON utm_user (user_aliker_id)');
        $this->addSql('ALTER TABLE log ADD CONSTRAINT FK_8F3F68C578E1CD FOREIGN KEY (utm_user_id) REFERENCES utm_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE log ADD CONSTRAINT FK_8F3F68C55F4CC3FE FOREIGN KEY (user_aliker_id) REFERENCES user_aliker (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE utm_user ADD CONSTRAINT FK_3B4BF96E5F4CC3FE FOREIGN KEY (user_aliker_id) REFERENCES user_aliker (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE log DROP CONSTRAINT FK_8F3F68C578E1CD');
        $this->addSql('DROP SEQUENCE log_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE utm_user_id_seq CASCADE');
        $this->addSql('DROP TABLE log');
        $this->addSql('DROP TABLE utm_user');
    }
}
