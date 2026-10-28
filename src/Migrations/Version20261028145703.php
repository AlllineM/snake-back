<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261028145703 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE level ADD user_name VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE level ADD difficulty INT DEFAULT NULL');
        $this->addSql('ALTER TABLE level ADD rating INT DEFAULT NULL');
        $this->addSql('ALTER TABLE level ADD target_score INT DEFAULT NULL');
        $this->addSql('ALTER TABLE level ADD type INT DEFAULT NULL');
        $this->addSql('ALTER TABLE level ADD alloted_time INT DEFAULT NULL');
        $this->addSql('ALTER TABLE level ADD author_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE level ADD life_max_count INT DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE level DROP user_name');
        $this->addSql('ALTER TABLE level DROP difficulty');
        $this->addSql('ALTER TABLE level DROP rating');
        $this->addSql('ALTER TABLE level DROP target_score');
        $this->addSql('ALTER TABLE level DROP type');
        $this->addSql('ALTER TABLE level DROP alloted_time');
        $this->addSql('ALTER TABLE level DROP author_id');
        $this->addSql('ALTER TABLE level DROP life_max_count');
    }
}
