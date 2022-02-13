<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220213112126 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'updated_at not nullable and == created_at for never edited task and docs. Revert Migration Version20220104200752';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('UPDATE doc SET updated_at = created_at WHERE updated_at is null');
        $this->addSql('UPDATE task SET updated_at = created_at WHERE updated_at is null');
        $this->addSql('ALTER TABLE doc CHANGE updated_at updated_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE task CHANGE updated_at updated_at DATETIME NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('UPDATE doc SET updated_at = NULL WHERE updated_at = created_at');
        $this->addSql('UPDATE task SET updated_at = NULL WHERE updated_at = created_at');
        $this->addSql('ALTER TABLE doc CHANGE updated_at updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE task CHANGE updated_at updated_at DATETIME DEFAULT NULL');
    }
}
