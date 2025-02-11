<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250211072412 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Uploaded file entity';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE file (id INT AUTO_INCREMENT NOT NULL, created_by VARCHAR(80) DEFAULT NULL, caption VARCHAR(80) NOT NULL, filename VARCHAR(80) NOT NULL, target VARCHAR(8) NOT NULL,type VARCHAR(8) NOT NULL, mime_type VARCHAR(80) NOT NULL, size_bytes INT NOT NULL, description VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_8C9F3610DE12AB56 (created_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE file ADD CONSTRAINT FK_8C9F3610DE12AB56 FOREIGN KEY (created_by) REFERENCES app_user (username)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE file DROP FOREIGN KEY FK_8C9F3610DE12AB56');
        $this->addSql('DROP TABLE file');
    }
}
