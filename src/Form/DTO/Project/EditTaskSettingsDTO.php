<?php
/**
 * User: demius
 * Date: 13.01.2022
 * Time: 0:17
 */
declare(strict_types=1);

namespace App\Form\DTO\Project;

use App\Model\Dto\Dictionary\Dictionary;
use App\Model\Dto\Project\TaskSettings;
use App\Service\Constraints\ValidDictionary;
use JsonException;
use Symfony\Component\Validator\Constraints as Assert;

class EditTaskSettingsDTO
{
    /**
     * @Assert\Json()
     * @ValidDictionary("task.type")
     */
    private string $types;

    /**
     * @Assert\Json()
     * @ValidDictionary("task.stage")
     */
    private string $stages;

    /**
     * @Assert\Json()
     * @ValidDictionary("task.priority")
     */
    private string $priority;

    /**
     * @Assert\Json()
     * @ValidDictionary("task.complexity")
     */
    private string $complexity;

    /**
     * @param TaskSettings $taskSettings
     * @throws JsonException
     */
    public function __construct(TaskSettings $taskSettings)
    {
        $this->types = $this->dictionaryToString($taskSettings->getTypes());
        $this->stages = $this->dictionaryToString($taskSettings->getStages());
        $this->priority = $this->dictionaryToString($taskSettings->getPriority());
        $this->complexity = $this->dictionaryToString($taskSettings->getComplexity());
    }

    /**
     * @param Dictionary $dictionary
     * @return string
     * @throws JsonException
     */
    private function dictionaryToString(Dictionary $dictionary): string
    {
        return json_encode(
            $dictionary->jsonSerialize(),
            JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE
        );
    }

    /**
     * @return string
     */
    public function getTypes(): string
    {
        return $this->types;
    }

    /**
     * @param string $types
     * @return EditTaskSettingsDTO
     */
    public function setTypes(string $types): EditTaskSettingsDTO
    {
        $this->types = $types;
        return $this;
    }

    /**
     * @return string
     */
    public function getStages(): string
    {
        return $this->stages;
    }

    /**
     * @param string $stages
     * @return EditTaskSettingsDTO
     */
    public function setStages(string $stages): EditTaskSettingsDTO
    {
        $this->stages = $stages;
        return $this;
    }

    /**
     * @return string
     */
    public function getPriority(): string
    {
        return $this->priority;
    }

    /**
     * @param string $priority
     * @return EditTaskSettingsDTO
     */
    public function setPriority(string $priority): EditTaskSettingsDTO
    {
        $this->priority = $priority;
        return $this;
    }

    /**
     * @return string
     */
    public function getComplexity(): string
    {
        return $this->complexity;
    }

    /**
     * @param string $complexity
     * @return EditTaskSettingsDTO
     */
    public function setComplexity(string $complexity): EditTaskSettingsDTO
    {
        $this->complexity = $complexity;
        return $this;
    }
}