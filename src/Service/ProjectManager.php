<?php
/**
 * User: demius
 * Date: 11.08.2021
 * Time: 14:32
 */
declare(strict_types=1);

namespace App\Service;

use App\Entity\Project;
use App\Repository\ProjectRepository;
use App\Security\UserPermissionsEnum;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class ProjectManager
{
    /**
     * @var ProjectRepository
     */
    private ProjectRepository $projectRepository;

    private RequestStack $requestStack;

    /**
     * @var array
     * Кеш имен проектов по суффиксам.
     * @TODO вынести в кеш, живущий между запросами
     */
    private array $projectNames = [];

    /**
     * Текущий проект, имеющий смысл в пределах одного запроса
     * @var Project|null
     */
    private ?Project $currentProject = null;


    public function __construct(ProjectRepository $projectRepository, RequestStack $requestStack)
    {
        $this->projectRepository = $projectRepository;
        $this->requestStack = $requestStack;
    }

    public function findCurrentProject(): void
    {
        $request = $this->requestStack->getCurrentRequest();
        if ($request) {
            $suffix = $this->getSuffix($request);
            if ($suffix) {
                $this->currentProject = $this->projectRepository->findBySuffix($suffix);
            }
        }
    }

    /**
     * Получить текущий проект, если в текущем контексте он возможен.
     * (некоторый аналог Security->getUser())
     * @return Project|null
     */
    public function getProject(): ?Project
    {
        return $this->currentProject;
    }

    /**
     * Относится ли текущий запрос к зоне проектов
     * @return bool
     */
    public function isProjectContext(): bool
    {
        return (bool) $this->currentProject;
    }

    private function getSuffix(Request $request): ?string
    {
        $suffix = $request->get('suffix');
        if ($suffix) {
            return $suffix;
        }

        $taskId = $request->get('taskId');
        if ($taskId && preg_match('/^(\w+)-\d+$/', $taskId, $matches) && !empty($matches[1])) {
            return $matches[1];
        }

        $docId = $request->get('docId');
        if ($taskId && preg_match('/^(\w+)#\d+$/', $docId, $matches) && !empty($matches[1])) {
            return $matches[1];
        }

        return null;
    }

    /**
     * Если нам нужно только имя проекта, без целого объекта (вобще по логике это относится к репозиторию)
     * @param string $projectSuffix
     * @return string
     */
    public function getNameBySuffix(string $projectSuffix): string
    {
        if ($this->currentProject && $this->currentProject->getSuffix() === $projectSuffix) {
            $this->projectNames[$projectSuffix] = $this->currentProject->getSuffix();
        }

        if (!isset($this->projectNames[$projectSuffix])) {
            $qb = $this->projectRepository->createQueryBuilder('p');
            $qb->select('p.name')
                ->where($qb->expr()->eq('p.suffix', ':suffix'))
                ->setParameter('suffix', $projectSuffix)
                ->setMaxResults(1);

            $this->projectNames[$projectSuffix] = $qb->getQuery()->getSingleScalarResult();
        }
        return $this->projectNames[$projectSuffix];
    }

}