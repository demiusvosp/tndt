<?php
/**
 * User: demius
 * Date: 14.12.2021
 * Time: 22:16
 */
declare(strict_types=1);

namespace App\Form\DTO\Task;

use App\Entity\Contract\InProjectInterface;
use App\Service\Constraints\DictionaryValue;
use Symfony\Component\Validator\Constraints as Assert;

class CloseTaskDTO implements InProjectInterface
{
    /**
     * @var string
     */
    private string $suffix;

    /**
     * @var int
     * @DictionaryValue("task.stage")
     */
    private int $stage;

    /**
     * @var string
     * @Assert\Length(max=1000)
     */
    private string $comment = '';


    /**
     * @param string $projectSuffix - Суффикс проекта, в котором закрывается задача
     */
    public function __construct(string $projectSuffix)
    {
        $this->suffix = $projectSuffix;
    }

    public function getSuffix(): string
    {
        return $this->suffix;
    }

    /**
     * @return int
     */
    public function getStage(): int
    {
        return $this->stage;
    }

    /**
     * @param int $stage
     * @return CloseTaskDTO
     */
    public function setStage(int $stage): CloseTaskDTO
    {
        $this->stage = $stage;
        return $this;
    }

    /**
     * @return string
     */
    public function getComment(): string
    {
        return $this->comment;
    }

    /**
     * @param string|null $comment
     * @return CloseTaskDTO
     */
    public function setComment(?string $comment): CloseTaskDTO
    {
        $this->comment = (string) $comment;
        return $this;
    }

}