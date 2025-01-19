<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250119115302 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Indexes for task table for project task table page';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX isClosed ON task');
        $this->addSql('CREATE INDEX idx_suffix_created_at ON task (suffix, created_at)');
        $this->addSql('CREATE INDEX idx_suffix_updated_at ON task (suffix, updated_at)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX idx_suffix_created_at ON task');
        $this->addSql('DROP INDEX idx_suffix_updated_at ON task');
        $this->addSql('CREATE INDEX isClosed ON task (is_closed)');
    }
}
