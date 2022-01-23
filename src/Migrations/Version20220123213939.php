<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220123213939 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add doc.state field and copy is_archived flag data. (Delete is_archived will be carried in next migration)';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE doc ADD state SMALLINT NOT NULL COMMENT \'(DC2Type:App\\\\Entity\\\\Doc\\\\DocStateEnum)\' AFTER is_archived');
        $this->addSql('UPDATE doc SET state = 2 WHERE is_archived = true'); // DocStateEnum::ARCHIVE = 2
    }

    public function down(Schema $schema): void
    {
        $this->addSql('UPDATE doc SET is_archived = true WHERE state = 2'); // DocStateEnum::ARCHIVE = 2
        $this->addSql('ALTER TABLE doc DROP state');
    }
}
