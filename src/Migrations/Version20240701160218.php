<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240701160218 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE fact_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE log_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE message_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE reaction_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE request_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE user_aliker_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE utm_user_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE weight_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE fact (id INT NOT NULL, author_id INT NOT NULL, body TEXT NOT NULL, date_time TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, status VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_6FA45B95F675F31B ON fact (author_id)');
        $this->addSql('CREATE TABLE log (id INT NOT NULL, utm_user_id INT DEFAULT NULL, user_aliker_id INT DEFAULT NULL, action VARCHAR(255) DEFAULT NULL, date_time TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_8F3F68C578E1CD ON log (utm_user_id)');
        $this->addSql('CREATE INDEX IDX_8F3F68C55F4CC3FE ON log (user_aliker_id)');
        $this->addSql('CREATE TABLE message (id INT NOT NULL, user_from_id INT NOT NULL, user_to_id INT NOT NULL, datetime TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, body TEXT NOT NULL, status VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_B6BD307F20C3C701 ON message (user_from_id)');
        $this->addSql('CREATE INDEX IDX_B6BD307FD2F7B13D ON message (user_to_id)');
        $this->addSql('CREATE TABLE reaction (id INT NOT NULL, fact_id INT NOT NULL, user_aliker_id INT NOT NULL, date_time TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, status VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_A4D707F7156D5C2A ON reaction (fact_id)');
        $this->addSql('CREATE INDEX IDX_A4D707F75F4CC3FE ON reaction (user_aliker_id)');
        $this->addSql('CREATE TABLE request (id INT NOT NULL, user_from_id INT NOT NULL, user_to_id INT NOT NULL, datetime TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, status INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_3B978F9F20C3C701 ON request (user_from_id)');
        $this->addSql('CREATE INDEX IDX_3B978F9FD2F7B13D ON request (user_to_id)');
        $this->addSql('CREATE TABLE telegram_user (id INT NOT NULL, user_aliker_id INT DEFAULT NULL, username VARCHAR(255) DEFAULT NULL, first_name VARCHAR(255) DEFAULT NULL, last_name VARCHAR(255) DEFAULT NULL, is_push_allowed BOOLEAN DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F180F0595F4CC3FE ON telegram_user (user_aliker_id)');
        $this->addSql('CREATE TABLE user_aliker (id INT NOT NULL, phone VARCHAR(180) DEFAULT NULL, roles JSON NOT NULL, password VARCHAR(255) DEFAULT NULL, nick VARCHAR(255) DEFAULT NULL, balance INT NOT NULL, rating INT NOT NULL, email VARCHAR(255) DEFAULT NULL, api_token VARCHAR(255) DEFAULT NULL, last_online TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_EFD7EF97444F97DD ON user_aliker (phone)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_EFD7EF977BA2F5EB ON user_aliker (api_token)');
        $this->addSql('CREATE TABLE utm_user (id INT NOT NULL, user_aliker_id INT DEFAULT NULL, utm_source VARCHAR(255) DEFAULT NULL, utm_medium VARCHAR(255) DEFAULT NULL, utm_campaign VARCHAR(255) DEFAULT NULL, utm_term VARCHAR(255) DEFAULT NULL, utm_content VARCHAR(255) DEFAULT NULL, create_date_time TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, temporary_id VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_3B4BF96E5F4CC3FE ON utm_user (user_aliker_id)');
        $this->addSql('CREATE TABLE weight (id INT NOT NULL, reaction_one VARCHAR(255) NOT NULL, reaction_two VARCHAR(255) NOT NULL, weight INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE fact ADD CONSTRAINT FK_6FA45B95F675F31B FOREIGN KEY (author_id) REFERENCES user_aliker (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE log ADD CONSTRAINT FK_8F3F68C578E1CD FOREIGN KEY (utm_user_id) REFERENCES utm_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE log ADD CONSTRAINT FK_8F3F68C55F4CC3FE FOREIGN KEY (user_aliker_id) REFERENCES user_aliker (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F20C3C701 FOREIGN KEY (user_from_id) REFERENCES user_aliker (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FD2F7B13D FOREIGN KEY (user_to_id) REFERENCES user_aliker (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE reaction ADD CONSTRAINT FK_A4D707F7156D5C2A FOREIGN KEY (fact_id) REFERENCES fact (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE reaction ADD CONSTRAINT FK_A4D707F75F4CC3FE FOREIGN KEY (user_aliker_id) REFERENCES user_aliker (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE request ADD CONSTRAINT FK_3B978F9F20C3C701 FOREIGN KEY (user_from_id) REFERENCES user_aliker (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE request ADD CONSTRAINT FK_3B978F9FD2F7B13D FOREIGN KEY (user_to_id) REFERENCES user_aliker (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE telegram_user ADD CONSTRAINT FK_F180F0595F4CC3FE FOREIGN KEY (user_aliker_id) REFERENCES user_aliker (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE utm_user ADD CONSTRAINT FK_3B4BF96E5F4CC3FE FOREIGN KEY (user_aliker_id) REFERENCES user_aliker (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE level ADD status INT DEFAULT NULL');
        $this->addSql('ALTER TABLE level ADD start_speed INT DEFAULT NULL');
        $this->addSql('ALTER TABLE level ADD boost_speed INT DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE reaction DROP CONSTRAINT FK_A4D707F7156D5C2A');
        $this->addSql('ALTER TABLE fact DROP CONSTRAINT FK_6FA45B95F675F31B');
        $this->addSql('ALTER TABLE log DROP CONSTRAINT FK_8F3F68C55F4CC3FE');
        $this->addSql('ALTER TABLE message DROP CONSTRAINT FK_B6BD307F20C3C701');
        $this->addSql('ALTER TABLE message DROP CONSTRAINT FK_B6BD307FD2F7B13D');
        $this->addSql('ALTER TABLE reaction DROP CONSTRAINT FK_A4D707F75F4CC3FE');
        $this->addSql('ALTER TABLE request DROP CONSTRAINT FK_3B978F9F20C3C701');
        $this->addSql('ALTER TABLE request DROP CONSTRAINT FK_3B978F9FD2F7B13D');
        $this->addSql('ALTER TABLE telegram_user DROP CONSTRAINT FK_F180F0595F4CC3FE');
        $this->addSql('ALTER TABLE utm_user DROP CONSTRAINT FK_3B4BF96E5F4CC3FE');
        $this->addSql('ALTER TABLE log DROP CONSTRAINT FK_8F3F68C578E1CD');
        $this->addSql('DROP SEQUENCE fact_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE log_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE message_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE reaction_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE request_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE user_aliker_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE utm_user_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE weight_id_seq CASCADE');
        $this->addSql('DROP TABLE fact');
        $this->addSql('DROP TABLE log');
        $this->addSql('DROP TABLE message');
        $this->addSql('DROP TABLE reaction');
        $this->addSql('DROP TABLE request');
        $this->addSql('DROP TABLE telegram_user');
        $this->addSql('DROP TABLE user_aliker');
        $this->addSql('DROP TABLE utm_user');
        $this->addSql('DROP TABLE weight');
        $this->addSql('ALTER TABLE level DROP status');
        $this->addSql('ALTER TABLE level DROP start_speed');
        $this->addSql('ALTER TABLE level DROP boost_speed');
    }
}
