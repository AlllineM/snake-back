<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230228122954 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE fact ADD status VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE weight ALTER reaction_one TYPE INT');
        $this->addSql('ALTER TABLE weight ALTER reaction_one DROP DEFAULT');
        $this->addSql('ALTER TABLE weight ALTER reaction_two TYPE INT');
        $this->addSql('ALTER TABLE weight ALTER reaction_two DROP DEFAULT');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE fact DROP status');
        $this->addSql('ALTER TABLE weight ALTER reaction_one TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE weight ALTER reaction_one DROP DEFAULT');
        $this->addSql('ALTER TABLE weight ALTER reaction_two TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE weight ALTER reaction_two DROP DEFAULT');
    }
}
