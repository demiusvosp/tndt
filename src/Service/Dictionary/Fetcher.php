<?php
/**
 * User: demius
 * Date: 20.11.2021
 * Time: 23:11
 */
declare(strict_types=1);

namespace App\Service\Dictionary;

use App\Contract\InProjectInterface;
use App\Contract\WithProjectInterface;
use App\Entity\Project;
use App\Exception\DictionaryException;
use App\Model\Dto\Dictionary\Dictionary;
use App\Model\Dto\Dictionary\DictionaryItem;
use App\Model\Enum\DictionaryTypeEnum;
use App\Repository\ProjectRepository;
use App\Service\ProjectContext;

/**
 * Система справочников. Позволяет получить по типу справочника и его значению его элемент
 */
class Fetcher
{
    private array $projects = [];

    private ProjectRepository $projectRepository;
    private ProjectContext $projectContext;

    public function __construct(ProjectRepository $projectRepository, ProjectContext $projectContext)
    {
        $this->projectRepository = $projectRepository;
        $this->projectContext = $projectContext;
    }

    /**
     * Получить указанный элемент справочника по указанному типу и сущности, хранящей значение справочника
     * По типу справочника определяет, где найти справочник и где найти его значение.
     * (Сущность должна имплементировать InProjectInterface, чтобы по ней получить проект, в котором хранится объект
     *    справочника. Строго говоря справочник необязательно хранится в проекте, но сейчас все справочники относятся
     *    к проектам, а когда будет не так, нужно будет создать систему хендлеров умеющий брать справочники из разных
     *    сущностей)
     * @param DictionaryTypeEnum $dictionaryType
     * @param InProjectInterface $entity
     * @return DictionaryItem
     * @throws DictionaryException
     */
    public function getDictionaryItem(DictionaryTypeEnum $dictionaryType, InProjectInterface $entity): DictionaryItem
    {
        $dictionaryObject = $this->getDictionary($dictionaryType, $entity);

        $valueGetter = $dictionaryType->getEntityGetter();
        $dictionaryValue = $entity->{$valueGetter}();

        return $dictionaryObject->getItem($dictionaryValue);
    }

    /**
     * Получить указанный справочник из указанного проекта (или любой сущности, связанной с проектом)
     * @param DictionaryTypeEnum $dictionaryType
     * @param InProjectInterface|WithProjectInterface|string|null $entity - null - текущий проект из сервиса ProjectContext
     * @return Dictionary
     * @throws DictionaryException
     */
    public function getDictionary(DictionaryTypeEnum $dictionaryType, InProjectInterface|WithProjectInterface|string|null $entity): Dictionary
    {
        $object = null;
        if (is_string($entity)) {
            $object = $this->loadProject($entity);
        }

        if ($entity instanceof WithProjectInterface) {
            $entity = $entity->getProject();
        }
        if ($entity instanceof Project) {
            $this->projects[$entity->getSuffix()] = $entity;
            $object = $entity;
        }

        if ($entity instanceof InProjectInterface) {
            $object = $this->loadProject($entity->getSuffix());
        }

        if (!$object) {
            throw new DictionaryException(
                'Не удалось получить проект, хранящий справочник, по переданным данным '
                . (is_string($entity) ? $entity : get_class($entity))
            );
        }

        foreach ($dictionaryType->getSource() as $method) {
            $object = $object->{$method}();
        }

        if (!$object instanceof Dictionary) {
            throw new DictionaryException(
                'Не удалось получить справочник по указанному по источнику '
                . implode('->', $dictionaryType->getSource())
            );
        }

        return $object;
    }

    /**
     * Получить все, связанные с объектом словари
     * @param string $entityClass
     * @param \App\Contract\InProjectInterface $entity
     * @return array
     * @throws DictionaryException
     */
    public function getDictionariesByEntityClass(string $entityClass, InProjectInterface $entity): array
    {
        $items = [];
        $dictionaryTypes = DictionaryTypeEnum::allFromEntity($entityClass);
        foreach ($dictionaryTypes as $type) {
            $items[$type->getValue()] = $this->getDictionary($type, $entity);
        }

        return $items;
    }

    /**
     * Получить элементы всех, связанных с объектом словарей.
     * @param InProjectInterface $entity
     * @return array
     * @throws DictionaryException
     */
    public function getRelatedItems(InProjectInterface $entity): array
    {
        $items = [];
        $dictionaryTypes = DictionaryTypeEnum::allFromEntity($entity);
        foreach ($dictionaryTypes as $type) {
            $items[$type->getValue()] = $this->getDictionaryItem($type, $entity);
        }

        return $items;
    }

    private function loadProject(string $suffix): ?Project
    {
        if (isset($this->projects[$suffix])) {
            return $this->projects[$suffix];
        }

        $project = $this->projectContext->getProject();
        if ($project && $project->getSuffix() === $suffix) {
            $this->projects[$suffix] = $project;
            return $project;
        }

        $project = $this->projectRepository->findBySuffix($suffix);
        if ($project) {
            $this->projects[$suffix] = $project;
            return $project;
        }

        return null;
    }
}