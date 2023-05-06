<?php
/**
 * User: demius
 * Date: 06.05.2023
 * Time: 12:07
 */

namespace App\Tests\units\Security\Voters;

use App\Security\Hierarchy\HierarchyHelper;
use App\Security\UserPermissionsEnum;
use App\Security\Voter\GlobalRolesVoter;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class GlobalRolesVoterTest extends TestCase
{
    private GlobalRolesVoter $globalRolesVoter;

    /** @var HierarchyHelper|MockObject */
    private $hierarchyHelper;
    /** @var LoggerInterface|MockObject */
    private $logger;

    public function setUp(): void
    {
        $this->hierarchyHelper = new HierarchyHelper(new ArrayAdapter());
        $this->hierarchyHelper->buildMap(UserPermissionsEnum::getHierarchy());
        $this->logger = $this->createMock(LoggerInterface::class);

        $this->globalRolesVoter = new GlobalRolesVoter($this->hierarchyHelper);
        $this->globalRolesVoter->setLogger($this->logger);
    }

    /** @dataProvider dataset */
    public function test(array $userRoles, $subject, array $attributes, int $expected)
    {
//        /** @var TokenInterface|MockObject $token */
//        Не работает пока TokenInterface::getRoleNames() не обявлено напрямую, ждем обновления симфони
//        $token = $this->createMock(TokenInterface::class)
//            ->method('getRoleNames')
//            ->willReturn($userRoles);
        $token = new UsernamePasswordToken('abc', 'creds', 'key', $userRoles);

        $result = $this->globalRolesVoter->vote($token, $subject, $attributes);
        $this->assertSame($expected, $result);
    }

    public function dataset(): iterable
    {
        // root можно все
        yield [['ROLE_ROOT'], '', ['ROLE_ROOT'], VoterInterface::ACCESS_GRANTED];
        yield [['ROLE_ROOT'], '', ['PERM_USER_LIST'], VoterInterface::ACCESS_GRANTED];
        yield [['ROLE_ROOT'], '', ['PERM_TASK_CREATE'], VoterInterface::ACCESS_GRANTED];

        // что можно пользователю
        yield [['ROLE_USER'], '', ['ROLE_ROOT'], VoterInterface::ACCESS_ABSTAIN];
        yield [['ROLE_USER'], '', ['PERM_USER_LIST'], VoterInterface::ACCESS_GRANTED];
        yield [['ROLE_USER'], '', ['PERM_USER_CREATE'], VoterInterface::ACCESS_ABSTAIN];

        // что можно гостю
        yield [[''], '', ['ROLE_ROOT'], VoterInterface::ACCESS_ABSTAIN];
        yield [[''], '', ['ROLE_ROOT'], VoterInterface::ACCESS_ABSTAIN];
        yield [[''], '', ['ROLE_ROOT'], VoterInterface::ACCESS_ABSTAIN];
    }
}