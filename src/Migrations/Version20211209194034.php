<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211209194034 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add task.stage dictionary to Task entity';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE task ADD stage INT NOT NULL AFTER is_closed');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE task DROP stage');
    }
}
