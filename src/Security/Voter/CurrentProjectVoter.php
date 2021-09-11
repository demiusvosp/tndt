<?php
/**
 * User: demius
 * Date: 10.09.2021
 * Time: 0:32
 */
declare(strict_types=1);

namespace App\Security\Voter;

use App\Security\Hierarchy\HierarchyHelper;
use App\Security\UserPermissionsEnum;
use App\Security\UserRolesEnum;
use App\Service\ProjectManager;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

/**
 * Избиратель для текущего проекта. Работает только для запросов имеющих currentProject (т.е. тех, в которые был
 * передан проект, например страниц внутри проекта)
 */
class CurrentProjectVoter implements VoterInterface, LoggerAwareInterface
{
    private HierarchyHelper $hierarchyHelper;
    private ProjectManager $projectManager;
    private LoggerInterface $logger;

    public function __construct(
        HierarchyHelper $hierarchyHelper,
        ProjectManager $projectManager
    ) {
        $this->hierarchyHelper = $hierarchyHelper;
        $this->projectManager = $projectManager;
    }

    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    public function vote(TokenInterface $token, $subject, array $attributes): int
    {
        if(!$this->projectManager->isProjectContext()) {
            $this->logger->debug('Not in current project constext - abstain');
            // способны обработать только web-страницы с переданным project
            return VoterInterface::ACCESS_ABSTAIN;
        }

        foreach ($token->getRoleNames() as $fullRoleName) {
            if (UserRolesEnum::isValid($fullRoleName)) {
                $this->logger->debug('Its global role - skip', ['role' => $fullRoleName]);
                // это глобальная роль, пусть с ней RoleVoter разбирается
                continue;
            }
            [$role, $project] = UserRolesEnum::explodeSyntheticRole($fullRoleName);
            if (empty($role) || empty($project)) {
                $this->logger->debug('Its incorrect role - skip', ['role' => $fullRoleName]);
                // это не роль в проекте
                continue;
            }
            /** @noinspection NullPointerExceptionInspection мы уже проверили наличие currentProject */
            if ($project !== $this->projectManager->getProject()->getSuffix()) {
                $this->logger->debug(
                    'Its another project role - skip',
                    ['currentProject' => $this->projectManager->getProject()->getSuffix(), 'requestedProject' => $project]
                );
                // роль не этого проекта
                continue;
            }
            if (!UserRolesEnum::isValid($role)) {
                $this->logger->debug('Its invalid self project role - critical error', ['role' => $role]);
                // неизвестная роль
                throw new \DomainException('Unknown '.$role.' role');
            }

            foreach ($attributes as $attribute) {
                if ($attribute === UserRolesEnum::ROLE_ROOT) {
                    $this->logger->debug('Its check root attribute - skip');
                    // действия требующие root пусть RootVoter разбирает
                    return VoterInterface::ACCESS_ABSTAIN;
                }
                if (UserPermissionsEnum::isValid($attribute)) {
                    $this->logger->debug('Start check attribute '.$attribute);
                    if ($this->hierarchyHelper->has($attribute, $role)) {
                        $this->logger->debug('Its project '.$project.' - grant', ['attribute' => $attribute, 'role' => $role]);
                        return VoterInterface::ACCESS_GRANTED;
                    }
                }
            }
        }

        return VoterInterface::ACCESS_ABSTAIN;
    }
}