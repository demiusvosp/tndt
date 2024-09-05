<?php

declare(strict_types=1);

namespace App\Migrations;

use App\Entity\User;
use App\Model\Enum\Security\UserRolesEnum;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use DomainException;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211104170351_CreateRootUser extends AbstractMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function getDescription() : string
    {
        return 'Create root user';
    }

    /**
     * @throws DomainException
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception
     * @throws \JsonException
     */
    public function up(Schema $schema) : void
    {
        $checkRoot = $this->connection->executeQuery('SELECT id FROM tndt.app_user WHERE app_user.username = "root";');

        if (($existRoot = $checkRoot->fetchOne()) === false) {
            $passwordHasher = $this->container->get('security.user_password_hasher');
            if (!$passwordHasher) {
                throw new DomainException('Cannot find security.user_password_hasher service');
            }
            $root = new User('root');

            $this->addSql('INSERT INTO tndt.app_user (id, username, name,  email, roles, password, locked, created_at) 
                VALUES (1, "root", "root", "", :roles, :password, false, NOW())',
                [
                    'roles' => json_encode([UserRolesEnum::ROLE_ROOT], JSON_THROW_ON_ERROR),
                    'password' => $passwordHasher->hashPassword($root, 'root')
                ]
            );

        } else {
            $this->warnIf(true, 'Root user already exist in id: ' . $existRoot);
        }

    }

    public function down(Schema $schema) : void
    {
        $this->addSql('DELETE tndt.app_user WHERE username="root"');
    }
}
