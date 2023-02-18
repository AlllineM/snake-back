<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230218131952 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql("CREATE TYPE reaction_status AS ENUM ('same', 'like', 'skip', 'dislike', 'author')");

        $this->addSql('ALTER TABLE reaction DROP COLUMN status');
        $this->addSql('ALTER TABLE reaction ADD status reaction_status NOT NULL');

        $this->addSql('ALTER TABLE weight DROP COLUMN reaction_one');
        $this->addSql('ALTER TABLE weight DROP COLUMN reaction_two');

        $this->addSql('ALTER TABLE weight ADD reaction_one reaction_status NOT NULL');
        $this->addSql('ALTER TABLE weight ADD reaction_two reaction_status NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE reaction DROP COLUMN status');
        $this->addSql('ALTER TABLE reaction ADD status INT NOT NULL');

        $this->addSql('ALTER TABLE weight DROP COLUMN reaction_one');
        $this->addSql('ALTER TABLE weight DROP COLUMN reaction_two');

        $this->addSql('ALTER TABLE weight ADD reaction_one INT NOT NULL');
        $this->addSql('ALTER TABLE weight ADD reaction_two INT NOT NULL');

        $this->addSql('DROP TYPE reaction_status');
    }
}
