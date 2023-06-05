<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version00000000000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE app_user (username VARCHAR(80) NOT NULL, id INT NOT NULL, name VARCHAR(80) NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, locked TINYINT(1) NOT NULL, roles JSON NOT NULL, created_at DATETIME NOT NULL, last_login DATETIME DEFAULT NULL, PRIMARY KEY(username)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE doc (id INT AUTO_INCREMENT NOT NULL, suffix VARCHAR(8) NOT NULL, created_by VARCHAR(80) DEFAULT NULL, updated_by VARCHAR(80) DEFAULT NULL, no INT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, is_archived TINYINT(1) NOT NULL, caption VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, abstract TEXT NOT NULL, body LONGTEXT NOT NULL, INDEX IDX_8641FD64B5B087DE (suffix), INDEX IDX_8641FD64DE12AB56 (created_by), INDEX IDX_8641FD6416FE72E1 (updated_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE project (suffix VARCHAR(8) NOT NULL, name VARCHAR(255) NOT NULL, icon VARCHAR(100) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, is_archived TINYINT(1) NOT NULL, is_public TINYINT(1) NOT NULL, description LONGTEXT NOT NULL, INDEX isArchived (is_archived), INDEX isPublic (is_public), PRIMARY KEY(suffix)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE project_user (suffix VARCHAR(8) NOT NULL, username VARCHAR(80) NOT NULL, role VARCHAR(255) NOT NULL, INDEX IDX_B4021E51B5B087DE (suffix), INDEX IDX_B4021E51F85E0677 (username), UNIQUE INDEX idx_project_user (suffix, username), PRIMARY KEY(suffix, username)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE task (id INT AUTO_INCREMENT NOT NULL, suffix VARCHAR(8) NOT NULL, created_by VARCHAR(80) DEFAULT NULL, assigned_to VARCHAR(80) DEFAULT NULL, no INT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, is_closed TINYINT(1) NOT NULL, caption VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, INDEX IDX_527EDB25B5B087DE (suffix), INDEX IDX_527EDB25DE12AB56 (created_by), INDEX IDX_527EDB2589EEAF91 (assigned_to), INDEX isClosed (is_closed), UNIQUE INDEX idx_full_no (suffix, no), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE doc ADD CONSTRAINT FK_8641FD64B5B087DE FOREIGN KEY (suffix) REFERENCES project (suffix)');
        $this->addSql('ALTER TABLE doc ADD CONSTRAINT FK_8641FD64DE12AB56 FOREIGN KEY (created_by) REFERENCES app_user (username)');
        $this->addSql('ALTER TABLE doc ADD CONSTRAINT FK_8641FD6416FE72E1 FOREIGN KEY (updated_by) REFERENCES app_user (username)');
        $this->addSql('ALTER TABLE project_user ADD CONSTRAINT FK_B4021E51B5B087DE FOREIGN KEY (suffix) REFERENCES project (suffix)');
        $this->addSql('ALTER TABLE project_user ADD CONSTRAINT FK_B4021E51F85E0677 FOREIGN KEY (username) REFERENCES app_user (username)');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT FK_527EDB25B5B087DE FOREIGN KEY (suffix) REFERENCES project (suffix)');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT FK_527EDB25DE12AB56 FOREIGN KEY (created_by) REFERENCES app_user (username)');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT FK_527EDB2589EEAF91 FOREIGN KEY (assigned_to) REFERENCES app_user (username)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE doc DROP FOREIGN KEY FK_8641FD64DE12AB56');
        $this->addSql('ALTER TABLE doc DROP FOREIGN KEY FK_8641FD6416FE72E1');
        $this->addSql('ALTER TABLE project_user DROP FOREIGN KEY FK_B4021E51F85E0677');
        $this->addSql('ALTER TABLE task DROP FOREIGN KEY FK_527EDB25DE12AB56');
        $this->addSql('ALTER TABLE task DROP FOREIGN KEY FK_527EDB2589EEAF91');
        $this->addSql('ALTER TABLE doc DROP FOREIGN KEY FK_8641FD64B5B087DE');
        $this->addSql('ALTER TABLE project_user DROP FOREIGN KEY FK_B4021E51B5B087DE');
        $this->addSql('ALTER TABLE task DROP FOREIGN KEY FK_527EDB25B5B087DE');
        $this->addSql('DROP TABLE app_user');
        $this->addSql('DROP TABLE doc');
        $this->addSql('DROP TABLE project');
        $this->addSql('DROP TABLE project_user');
        $this->addSql('DROP TABLE task');
    }
}
