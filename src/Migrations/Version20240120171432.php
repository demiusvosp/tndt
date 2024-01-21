<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240120171432 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add activity table';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE activity (uuid BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid_binary)\', actor VARCHAR(80) DEFAULT NULL, project VARCHAR(8) DEFAULT NULL, type VARCHAR(80) NOT NULL, created_at DATETIME NOT NULL, subject_type VARCHAR(8) NOT NULL, subject_id INT NOT NULL, add_info JSON NOT NULL, INDEX IDX_AC74095A447556F9 (actor), INDEX IDX_AC74095A2FB3D0EE (project), INDEX subject (subject_type, subject_id), INDEX createdAt (created_at), PRIMARY KEY(uuid)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE activity ADD CONSTRAINT FK_AC74095A447556F9 FOREIGN KEY (actor) REFERENCES app_user (username)');
        $this->addSql('ALTER TABLE activity ADD CONSTRAINT FK_AC74095A2FB3D0EE FOREIGN KEY (project) REFERENCES project (suffix)');
        $this->addSql('ALTER TABLE project_user CHANGE suffix suffix VARCHAR(8) NOT NULL, CHANGE username username VARCHAR(80) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE activity DROP FOREIGN KEY FK_AC74095A447556F9');
        $this->addSql('ALTER TABLE activity DROP FOREIGN KEY FK_AC74095A2FB3D0EE');
        $this->addSql('DROP TABLE activity');
        $this->addSql('ALTER TABLE project_user CHANGE suffix suffix VARCHAR(8) DEFAULT NULL, CHANGE username username VARCHAR(80) DEFAULT NULL');
    }
}
