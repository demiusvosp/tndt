<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230529175045 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'tndt-109 change comment owner entity class to comment owner entity type';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('UPDATE comment SET entity_type = :new_val WHERE entity_type = :old_val', ['new_val' => 'task', 'old_val' => 'App\Entity\Task']);
        $this->addSql('UPDATE comment SET entity_type = :new_val WHERE entity_type = :old_val', ['new_val' => 'doc', 'old_val' => 'App\Entity\Doc']);
    }

    public function down(Schema $schema): void
    {
        $this->addSql('UPDATE comment SET entity_type = :new_val WHERE entity_type = :old_val', ['new_val' => 'App\Entity\Task', 'old_val' => 'task']);
        $this->addSql('UPDATE comment SET entity_type = :new_val WHERE entity_type = :old_val', ['new_val' => 'App\Entity\Doc', 'old_val' => 'doc']);
    }
}
