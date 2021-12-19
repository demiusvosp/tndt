<?php
/**
 * User: demius
 * Date: 20.11.2021
 * Time: 23:11
 */
declare(strict_types=1);

namespace App\Dictionary;

use App\Entity\Contract\InProjectInterface;
use App\Dictionary\Object\Dictionary;
use App\Dictionary\Object\DictionaryItem;
use App\Repository\ProjectRepository;
use App\Service\ProjectContext;

/**
 * Система справочников. Позволяет получить по типу справочника и его значению его элемент
 */
class Fetcher
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
     * Получить указанный элемент справочника по указанному типу и сущности, хранящей значение справочника
     * По типу справочника определяет, где найти справочник и где найти его значение.
     * (Сущность должна имплементировать InProjectInterface, чтобы по ней получить проект, в котором хранится объект
     *    справочника. Строго говоря справочник необязательно хранится в проекте, но сейчас все справочники относятся
     *    к проектам, а когда будет не так, нужно будет создать систему хендлеров умеющий брать справочники из разных
     *    сущностей)
     * @param TypesEnum $dictionaryType
     * @param InProjectInterface $entity
     * @return DictionaryItem
     */
    public function getDictionaryItem(TypesEnum $dictionaryType, InProjectInterface $entity): DictionaryItem
    {
        $dictionaryObject = $this->getDictionary($dictionaryType, $entity);

        $valueGetter = $dictionaryType->getEntityGetter();
        $dictionaryValue = $entity->{$valueGetter}();

        return $dictionaryObject->getItem($dictionaryValue);
    }

    /**
     * Получить указанный справочник из указанного проекта (или любой сущности, связанной с проектом)
     * @param TypesEnum $dictionaryType
     * @param InProjectInterface|null $entity - null - текущий проект из сервиса ProjectContext
     * @return Dictionary
     */
    public function getDictionary(TypesEnum $dictionaryType, InProjectInterface $entity): Dictionary
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
        return $object;
    }

    /**
     * Получить все, связанные с объектом словари
     * @param string $entityClass
     * @param InProjectInterface $entity
     * @return array
     */
    public function getDictionariesByEntityClass(string $entityClass, InProjectInterface $entity): array
    {
        $items = [];
        $dictionaryTypes = TypesEnum::allFromEntity($entityClass);
        foreach ($dictionaryTypes as $type) {
            $items[$type->getValue()] = $this->getDictionary($type, $entity);
        }

        return $items;
    }

    /**
     * Получить элементы всех, связанных с объектом словарей.
     * @param InProjectInterface $entity
     * @return array
     */
    public function getRelatedItems(InProjectInterface $entity): array
    {
        $items = [];
        $dictionaryTypes = TypesEnum::allFromEntity($entity);
        foreach ($dictionaryTypes as $type) {
            $items[$type->getValue()] = $this->getDictionaryItem($type, $entity);
        }

        return $items;
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