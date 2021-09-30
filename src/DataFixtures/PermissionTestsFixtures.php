<?php
/**
 * User: demius
 * Date: 30.09.2021
 * Time: 19:41
 */
declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Project;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class PermissionTestsFixtures extends Fixture implements FixtureGroupInterface
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public static function getGroups(): array
    {
        return [FixtureGroups::GROUP_PERMISSION_TESTS];
    }

    public function load(ObjectManager $manager)
    {
        $alice = new User();
        $alice->setUsername('Alice');
        $alice->setPassword($this->passwordEncoder->encodePassword($alice, 'Alice'));
        $manager->persist($alice);

        $bob = new User();
        $bob->setUsername('Bob');
        $bob->setPassword($this->passwordEncoder->encodePassword($bob, 'Bob'));
        $manager->persist($bob);

        $publicProject = new Project('pub');
        $publicProject->setIsPublic(true);
        $publicProject->setPm($alice);
        $manager->persist($publicProject);

        $alicePrivateProject = new Project('alice');
        $alicePrivateProject->setIsPublic(false);
        $alicePrivateProject->setPm($alice);
        $manager->persist($alicePrivateProject);

        $bobPrivateProject = new Project('bob');
        $bobPrivateProject->setIsPublic(false);
        $bobPrivateProject->setPm($bob);
        $manager->persist($bobPrivateProject);

        $manager->flush();
    }
}