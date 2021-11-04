<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Entity\User;
use App\Security\UserRolesEnum;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

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
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function up(Schema $schema) : void
    {
        $checkRoot = $this->connection->executeQuery('SELECT id FROM tndt.app_user WHERE app_user.username = "root";');

        if (($existRoot = $checkRoot->fetchOne()) === false) {
            $passwordEncoder = $this->container->get(UserPasswordEncoderInterface::class);
            $root = new User('root');

            $this->addSql('INSERT INTO tndt.app_user (username, name,  email, roles, password, locked, created_at) 
                VALUES ("root", "root", "", :roles, :password, false, NOW())',
                ['roles' => [UserRolesEnum::ROLE_ROOT], 'password' => $passwordEncoder->encodePassword($root, 'root')]
            );

        } else {
            $this->warnIf(true, 'Root user already exist in id: ' . $existRoot);
        }

    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
