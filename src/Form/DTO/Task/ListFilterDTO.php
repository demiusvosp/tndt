<?php
/**
 * User: demius
 * Date: 28.08.2021
 * Time: 16:35
 */
declare(strict_types=1);

namespace App\Form\DTO\Task;

use App\Form\ToFindCriteriaInterface;

class ListFilterDTO implements ToFindCriteriaInterface
{
    /** @var string */
    private $project;

    /** @var bool */
    private $addClosed = true;

    /**
     * @return string
     */
    public function getProject(): string
    {
        return $this->project;
    }

    /**
     * @param string $project
     * @return ListFilterDTO
     */
    public function setProject(string $project): ListFilterDTO
    {
        $this->project = $project;
        return $this;
    }

    /**
     * @return bool
     */
    public function isAddClosed(): bool
    {
        return $this->addClosed;
    }

    /**
     * @param bool $addClosed
     * @return ListFilterDTO
     */
    public function setAddClosed(bool $addClosed): ListFilterDTO
    {
        $this->addClosed = $addClosed;
        return $this;
    }

    public function getFilterCriteria(): array
    {
        $criteria = [];

        if(!empty($this->project)) {
            $criteria['suffix'] = $this->project;
        }

        if (!$this->addClosed) {
            $criteria['isClosed'] = false;
        }

        return $criteria;
    }
}