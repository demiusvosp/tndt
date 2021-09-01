<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RootUserFixture extends Fixture implements FixtureGroupInterface
{
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
        $root->setEmail('');
        $root->setEnabled(true);
        $root->setRoles([User::ROLE_ROOT]);
        $root->setPassword($this->passwordEncoder->encodePassword($root, 'root'));
        $manager->persist($root);

        $manager->flush();
    }
}
