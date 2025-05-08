<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250501181225 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE attachment (entity_type VARCHAR(10) NOT NULL, entity_id INT NOT NULL, file_id INT NOT NULL, INDEX IDX_795FD9BB93CB796C (file_id), INDEX entity (entity_type, entity_id), PRIMARY KEY(file_id, entity_type, entity_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE file (id INT AUTO_INCREMENT NOT NULL, project VARCHAR(8) NOT NULL, created_by VARCHAR(80) DEFAULT NULL, caption VARCHAR(80) NOT NULL, filename VARCHAR(80) NOT NULL, target VARCHAR(12) NOT NULL, type VARCHAR(8) NOT NULL, mime_type VARCHAR(80) NOT NULL, size_bytes INT NOT NULL, description VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_8C9F36102FB3D0EE (project), INDEX IDX_8C9F3610DE12AB56 (created_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE attachment ADD CONSTRAINT FK_795FD9BB93CB796C FOREIGN KEY (file_id) REFERENCES file (id)');
        $this->addSql('ALTER TABLE file ADD CONSTRAINT FK_8C9F36102FB3D0EE FOREIGN KEY (project) REFERENCES project (suffix)');
        $this->addSql('ALTER TABLE file ADD CONSTRAINT FK_8C9F3610DE12AB56 FOREIGN KEY (created_by) REFERENCES app_user (username)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE attachment DROP FOREIGN KEY FK_795FD9BB93CB796C');
        $this->addSql('ALTER TABLE file DROP FOREIGN KEY FK_8C9F36102FB3D0EE');
        $this->addSql('ALTER TABLE file DROP FOREIGN KEY FK_8C9F3610DE12AB56');
        $this->addSql('DROP TABLE attachment');
        $this->addSql('DROP TABLE file');
    }
}
