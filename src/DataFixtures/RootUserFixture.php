<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RootUserFixture extends Fixture implements FixtureGroupInterface
{
    private const ROOT_PASSWORD = 'root';

    private UserPasswordEncoderInterface $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public static function getGroups(): array
    {
        return [FixtureGroups::GROUP_INSTALL];
    }

    public function load(ObjectManager $manager)
    {
        $root = new User();
        $root->setUsername('root');
        $root->setName('root');
        $root->setEmail('');
        $root->setLocked(false);
        $root->setRoles([User::ROLE_ROOT]);
        $root->setPassword($this->passwordEncoder->encodePassword($root, self::ROOT_PASSWORD));
        $manager->persist($root);

        $manager->flush();
    }
}
