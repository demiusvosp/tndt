<?php
/**
 * User: demius
 * Date: 10.09.2021
 * Time: 0:32
 */
declare(strict_types=1);

namespace App\Security\Voter;

use App\Entity\Project;
use App\Model\Enum\UserPermissionsEnum;
use App\Model\Enum\UserRolesEnum;
use App\Security\Hierarchy\HierarchyHelper;
use App\Service\ProjectContext;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

/**
 * Избиратель для текущего проекта. Работает только для запросов имеющих currentProject (т.е. тех, в которые был
 * передан проект, например страниц внутри проекта)
 */
class PrivateProjectVoter implements VoterInterface, LoggerAwareInterface
{
    private HierarchyHelper $hierarchyHelper;
    private ProjectContext $projectContext;
    private LoggerInterface $securityLogger;

    public function __construct(
        HierarchyHelper $hierarchyHelper,
        ProjectContext $projectContext
    ) {
        $this->hierarchyHelper = $hierarchyHelper;
        $this->projectContext = $projectContext;
    }

    public function setLogger(LoggerInterface $securityLogger): void
    {
        $this->securityLogger = $securityLogger;
    }

    public function vote(TokenInterface $token, $subject, array $attributes): int
    {
        if (empty($subject)) {
            // В противном случае проверяем роль для текущего в данном контексте проекта
            $subject = $this->projectContext->getProject();
        }
        if ($subject instanceof Project) {
            $subject = $subject->getSuffix();
        }

        if(!$subject) {
            $this->securityLogger->debug('Not given or current project - abstain');
            // способны обработать только web-страницы с переданным project
            return VoterInterface::ACCESS_ABSTAIN;
        }

        foreach ($token->getRoleNames() as $fullRoleName) {
            if (UserRolesEnum::isValid($fullRoleName)) {
                $this->securityLogger->debug('{role} is global role - skip', ['role' => $fullRoleName]);
                // это глобальная роль, пусть с ней RoleVoter разбирается
                continue;
            }
            [$role, $roleProject] = UserRolesEnum::explodeSyntheticRole($fullRoleName);
            if (empty($role) || empty($roleProject)) {
                $this->securityLogger->warning('{role} is incorrect project role - skip', ['role' => $fullRoleName]);
                // это не роль в проекте
                continue;
            }

            if ($roleProject !== $subject) {
                $this->securityLogger->debug(
                    'It [{role} - {roleProject}] is not subject {subjectProject} project - skip',
                    ['role' => $role, 'roleProject' => $roleProject, 'subjectProject' => $subject]
                );
                // роль не этого проекта
                continue;
            }
            if (!UserRolesEnum::isValid($role)) {
                $this->securityLogger->error('{role} is invalid self project role - critical error', ['role' => $role]);
                // неизвестная роль
                throw new \DomainException('Неизвестная роль ' . $role);
            }

            foreach ($attributes as $attribute) {
                if (UserPermissionsEnum::isValid($attribute) || UserRolesEnum::isProjectRole($attribute)) {
                    if ($this->hierarchyHelper->has($attribute, $role)) {
                        $this->securityLogger->debug(
                            'Project {project} use {role} by granted {attribute} - grant',
                            ['project' => $roleProject, 'attribute' => $attribute, 'role' => $role]
                        );
                        return VoterInterface::ACCESS_GRANTED;
                    }
                }
            }
        }

        return VoterInterface::ACCESS_ABSTAIN;
    }
}