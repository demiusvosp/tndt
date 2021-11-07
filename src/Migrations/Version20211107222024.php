<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211107222024 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE project_user DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE project_user ADD id VARCHAR(255) DEFAULT NULL FIRST');
        $this->addSql('UPDATE project_user SET id = CONCAT_WS("-", suffix, username)');
        $this->addSql('ALTER TABLE project_user CHANGE id id VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE project_user ADD PRIMARY KEY (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE project_user DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE project_user DROP id, CHANGE suffix suffix VARCHAR(8) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE project_user ADD PRIMARY KEY (suffix, username)');
    }
}
