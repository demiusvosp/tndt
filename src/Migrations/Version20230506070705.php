<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230506070705 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'rename User.roles to User.globalRoles. This field saves only global roles (ROOT, USER, MANAGER). $user->getRoles() get global and project roles';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE `app_user` CHANGE COLUMN `roles` `global_roles` JSON NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE `app_user` CHANGE COLUMN `global_roles` `roles` JSON NOT NULL');
    }
}
