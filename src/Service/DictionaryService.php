<?php
/**
 * User: demius
 * Date: 20.11.2021
 * Time: 23:11
 */
declare(strict_types=1);

namespace App\Service;

use App\Entity\Contract\InProjectInterface;
use App\Enum\DictionariesEnum;
use App\Object\Base\Dictionary;
use App\Object\Base\DictionaryItem;
use App\Repository\ProjectRepository;

/**
 * Система справочников. Позволяет получить по типу справочника и его значению его элемент
 */
class DictionaryService
{
    /**
     * @var array
     */
    private array $projects = [];

    private ProjectRepository $projectRepository;
    private ProjectContext $projectContext;

    public function __construct(ProjectRepository $projectRepository, ProjectContext $projectContext)
    {
        $this->projectRepository = $projectRepository;
        $this->projectContext = $projectContext;
    }

    /**
     * Получить указанный справочник по сущности
     * (Сущность должна имплементировать InProjectInterface, чтобы по ней получить проект, в котором хранится объект
     *    справочника. Строго говоря справочник необязательно хранится в проекте, но сейчас все справочники относятся
     *    к проектам, а когда будет не так, нужно будет создать систему хендлеров умеющий брать справочники из разных
     *    сущностей)
     * @param DictionariesEnum $dictionaryType
     * @param InProjectInterface $entity
     * @return DictionaryItem
     */
    public function getDictionaryItem(DictionariesEnum $dictionaryType, InProjectInterface $entity): DictionaryItem
    {
        if (!isset($this->projects[$entity->getSuffix()])) {
            $this->loadProject($entity->getSuffix());
        }

        $object = $this->projects[$entity->getSuffix()];
        foreach ($dictionaryType->getSource() as $method) {
            $object = $object->{$method}();
        }
        if (!$object instanceof Dictionary) {
            throw new \DomainException(
                'Не удалось получить справочник по указанному по источнику '
                . implode('->', $dictionaryType->getSource())
            );
        }
        $dictionaryObject = $object;

        $valueGetter = $dictionaryType->getEntityGetter();
        $dictionaryValue = $entity->{$valueGetter}();

        return $dictionaryObject->getItem($dictionaryValue);
    }

    private function loadProject(string $suffix): void
    {
        $project = $this->projectContext->getProject();
        if ($project && $project->getSuffix() === $suffix) {
            $this->projects[$suffix] = $project;
            return;
        }

        $project = $this->projectRepository->findBySuffix($suffix);
        if ($project) {
            $this->projects[$suffix] = $project;
        }
    }
}