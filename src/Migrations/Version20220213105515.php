<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220213105515 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Remove doc.is_archived';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE doc DROP is_archived');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE doc ADD is_archived SMALLINT NOT NULL DEFAULT 0 AFTER state');
    }
}
