<?php
/**
 * User: demius
 * Date: 03.10.2021
 * Time: 2:59
 */
declare(strict_types=1);

namespace App\Service\Filler;

use App\Dictionary\Object\Dictionary;
use App\Dictionary\TypesEnum;
use App\Entity\Project;
use App\Entity\ProjectUser;
use App\Exception\DictionaryException;
use App\Form\DTO\Project\EditProjectCommonDTO;
use App\Form\DTO\Project\EditProjectPermissionsDTO;
use App\Form\DTO\Project\EditTaskSettingsDTO;
use App\Form\DTO\Project\NewProjectDTO;
use App\Repository\UserRepository;
use App\Security\UserRolesEnum;
use \InvalidArgumentException;
use JsonException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ProjectFiller
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
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

    /**
     * @param EditTaskSettingsDTO $dto
     * @param Project $project
     * @throws InvalidArgumentException
     */
    public function fillTaskSettings(EditTaskSettingsDTO $dto, Project $project): void
    {
        $currentSetting = $project->getTaskSettings();

        $currentSetting->getTypes()->merge(
            $this->stringToDictionary(TypesEnum::TASK_TYPE(), $dto->getTypes())
        );
        $currentSetting->getStages()->merge(
            $this->stringToDictionary(TypesEnum::TASK_STAGE(), $dto->getStages())
        );
        $currentSetting->getPriority()->merge(
            $this->stringToDictionary(TypesEnum::TASK_PRIORITY(), $dto->getPriority())
        );
        $currentSetting->getComplexity()->merge(
            $this->stringToDictionary(TypesEnum::TASK_COMPLEXITY(), $dto->getComplexity())
        );
    }

    /**
     * @param TypesEnum $dictionaryType
     * @param string $string
     * @return Dictionary
     * @throws InvalidArgumentException
     * @throws DictionaryException
     */
    private function stringToDictionary(TypesEnum $dictionaryType, string $string): Dictionary
    {
        try {
            $array = json_decode($string, true, 512, JSON_THROW_ON_ERROR);

            return $dictionaryType->createDictionary($array);

        } catch (JsonException $e) {
            throw new DictionaryException('Не удалось десериализовать справочник ' . $dictionaryType->getLabel());
        } catch (DictionaryException $e) {
            throw new DictionaryException('Справочник ' . $dictionaryType->getLabel() . ', ' . $e->getMessage());
        }
    }
}