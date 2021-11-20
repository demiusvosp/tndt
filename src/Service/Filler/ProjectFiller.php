<?php
/**
 * User: demius
 * Date: 03.10.2021
 * Time: 2:59
 */
declare(strict_types=1);

namespace App\Service\Filler;

use App\Entity\Project;
use App\Entity\ProjectUser;
use App\Entity\User;
use App\Form\DTO\Project\EditProjectCommonDTO;
use App\Form\DTO\Project\EditProjectPermissionsDTO;
use App\Form\DTO\Project\NewProjectDTO;
use App\Object\Project\TaskSettings;
use App\Repository\UserRepository;
use App\Security\UserRolesEnum;
use \InvalidArgumentException;
use Symfony\Component\Security\Core\Security;

class ProjectFiller
{
    private UserRepository $userRepository;
    private Security $security;

    public function __construct(UserRepository $userRepository, Security $security)
    {
        $this->userRepository = $userRepository;
        $this->security = $security;
    }

    public function createProjectByForm(NewProjectDTO $dto): Project
    {
        $project = new Project($dto->getSuffix());
        $project->setName($dto->getName());
        $project->setIcon((string) $dto->getIcon());
        $project->setIsPublic($dto->isPublic());
        $project->setDescription($dto->getDescription());

        $pm = $this->userRepository->findByUsername($dto->getPm());
        if (!$pm) {
            throw new InvalidArgumentException('project.pm.error.not_found');
        }
        $project->setPm($pm);

        return $project;
    }

    public function fillCommonSetting(EditProjectCommonDTO $dto, Project $project): void
    {
        $project->setName($dto->getName());
        $project->setIcon((string) $dto->getIcon());
    }

    /**
     * @param EditProjectPermissionsDTO $dto
     * @param Project $project
     * @throws InvalidArgumentException - установить не удалось (возможно юзер не найден)
     */
    public function fillPermissionsSetting(EditProjectPermissionsDTO $dto, Project $project): void
    {
        $project->setIsPublic($dto->isPublic());

        // одним запросом вытянем всех нужных нам пользователей
        $newUsers = $this->userRepository->findAllByUsername(
            array_merge([$dto->getPm()], $dto->getStaff(), $dto->getVisitors())
        );

        if (empty($newUsers[$dto->getPm()])) {
            throw new InvalidArgumentException('project.pm.error.not_found');
        }
        $project->setPm($newUsers[$dto->getPm()]);

        $projectStaff = [];
        foreach ($dto->getStaff() as $user) {
            if (empty($newUsers[$user])) {
                throw new InvalidArgumentException('project.staff.error.not_found');
            }
            $projectUser = new ProjectUser($project, $newUsers[$user]);
            $projectUser->setRole(UserRolesEnum::PROLE_STAFF());
            $projectStaff[] = $projectUser;
        }
        $project->setProjectUsers($projectStaff, UserRolesEnum::PROLE_STAFF());

        $projectVisitors = [];
        foreach ($dto->getVisitors() as $user) {
            if (empty($newUsers[$user])) {
                throw new InvalidArgumentException('project.visitors.error.not_found');
            }
            $projectUser = new ProjectUser($project, $newUsers[$user]);
            $projectUser->setRole(UserRolesEnum::PROLE_VISITOR());
            $projectVisitors[] = $projectUser;
        }
        $project->setProjectUsers($projectVisitors, UserRolesEnum::PROLE_VISITOR());
    }

    public function fillTaskSettings(TaskSettings $settings, Project $project): void
    {
        $currentSetting = $project->getTaskSettings();

        $currentSetting->getTypes()->merge($settings->getTypes());
    }
}