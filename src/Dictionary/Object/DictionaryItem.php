<?php
/**
 * User: demius
 * Date: 11.11.2021
 * Time: 0:07
 */
declare(strict_types=1);

namespace App\Dictionary\Object;

use App\Exception\DictionaryException;
use App\Object\JlobObjectInterface;
use App\Service\Badges\BadgeEnum;

class DictionaryItem implements JlobObjectInterface
{
    /**
     * @var int - уникальный в пределах справочника id.
     * Неизменяемый, и удалять по хорошему должно быть нельзя
     */
    private int $id;

    /**
     * @var string
     */
    private string $name;

    /**
     * @var string
     */
    private string $description;

    private ?BadgeEnum $useBadge = null;


    public function __construct(array $args = [])
    {
        if (!empty($args)) {
            $this->id = $args['id'] ?? 0;
            $this->setFromArray($args);
        }
    }

    public function setFromArray(array $arg): void
    {
        if (!isset($arg['name'])) {
            throw new DictionaryException('Элемент справочника должен иметь имя');
        }
        $this->name = $arg['name'];
        $this->description = $arg['description'] ?? '';
        $this->useBadge = !empty($arg['useBadge']) ? BadgeEnum::from($arg['useBadge']) : null;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'useBadge' => $this->useBadge ? $this->useBadge->getValue() : '',
        ];
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

    /**
     * @return BadgeEnum|null
     */
    public function getUseBadge(): ?BadgeEnum
    {
        return $this->useBadge;
    }

    /**
     * @param BadgeEnum|string|null $useBadge
     * @return DictionaryItem
     */
    public function setUseBadge($useBadge): DictionaryItem
    {
        if ($useBadge instanceof BadgeEnum) {
            $this->useBadge = $useBadge;
        } elseif (is_string($useBadge)) {
            $this->useBadge = BadgeEnum::from($useBadge);
        } else {
            $this->useBadge = null;
        }

        return $this;
    }
}