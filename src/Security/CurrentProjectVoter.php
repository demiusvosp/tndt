<?php
/**
 * User: demius
 * Date: 10.09.2021
 * Time: 0:32
 */
declare(strict_types=1);

namespace App\Security;

use App\Service\ProjectManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

/**
 * Избиратель для текущего проекта. Работает только для запросов имеющих currentProject (т.е. тех, в которые был
 * передан проект, например страниц внутри проекта)
 */
class CurrentProjectVoter implements VoterInterface
{
    private ProjectManager $projectManager;
    private RequestStack $requestStack;

    public function __construct(ProjectManager $projectManager, RequestStack $requestStack)
    {
        $this->projectManager = $projectManager;
        $this->requestStack = $requestStack;
    }

    public function vote(TokenInterface $token, $subject, array $attributes)
    {
        $request = $this->requestStack->getCurrentRequest();
        if (!$request) {
            return VoterInterface::ACCESS_ABSTAIN;
        }
        $currentProject = $this->projectManager->getCurrentProject($request);
        if(!$currentProject) {
            // способны обработать только web-страницы с переданным project
            return VoterInterface::ACCESS_ABSTAIN;
        }

        foreach ($token->getRoleNames() as $fullRoleName) {
            if (UserRolesEnum::isValid($fullRoleName)) {
                // это глобальная роль, пусть с ней RoleVoter разбирается
                continue;
            }
            [$role, $project] = UserRolesEnum::explodeSyntheticRole($fullRoleName);
            if (empty($role) || empty($project)) {
                // это не роль в проекте
                continue;
            }
            if ($project !== $currentProject->getSuffix()) {
                // роль не этого проекта
                continue;
            }
dump($subject); dump($attributes);
            if ($role === $subject) {
                return VoterInterface::ACCESS_GRANTED;
            }
        }
        return VoterInterface::ACCESS_ABSTAIN;
    }
}