<?php
/**
 * User: demius
 * Date: 14.12.2021
 * Time: 22:16
 */
declare(strict_types=1);

namespace App\Form\DTO\Task;

use Symfony\Component\Validator\Constraints as Assert;

class CloseTaskDTO
{
    /**
     * @var int
     */
    private int $stage;

    /**
     * @var string
     * @Assert\Length(max=1000)
     */
    private string $comment = '';


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