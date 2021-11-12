<?php
/**
 * User: demius
 * Date: 11.11.2021
 * Time: 0:07
 */
declare(strict_types=1);

namespace App\Service\JsonEntity;

class DictionaryItem extends BaseJsonEntity
{
    private int $id;
    private string $name;
    private string $description;

    public function __construct($arg)
    {
        parent::__construct($arg);
        if (is_array($arg)) {
            $this->id = $arg['id'] ?? 0;
            $this->name = $arg['name'] ?? '';
            $this->description = $arg['decription'] ?? '';
        } elseif ($arg instanceof DictionaryItem) {
            $this->id = $arg->getId();
            $this->name = $arg->getName();
            $this->description = $arg->getDescription();
        }
    }

    public function jsonSerialize(): array
    {
        return ['id' => $this->id, 'name' => $this->name, 'description' => $this->description];
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return DictionaryItem
     */
    public function setId(int $id): DictionaryItem
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return DictionaryItem
     */
    public function setName(string $name): DictionaryItem
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return DictionaryItem
     */
    public function setDescription(string $description): DictionaryItem
    {
        $this->description = $description;
        return $this;
    }
}