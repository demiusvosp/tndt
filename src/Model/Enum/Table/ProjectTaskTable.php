<?php
/**
 * User: demius
 * Date: 10.12.2024
 * Time: 22:38
 */

namespace App\Model\Enum\Table;

use App\Entity\Project;
use App\Entity\Task;
use App\Model\Dto\Table\Filter\ProjectFilter;
use App\Model\Dto\Table\Filter\TaskStatusFilter;
use App\Model\Dto\Table\FilterQuery;
use App\Model\Dto\Table\SortQuery;
use App\ViewModel\Table\TableFilter;

class ProjectTaskTable implements TableSettingsInterface
{
    private Project $project;

    public function __construct(Project $project)
    {
        $this->project = $project;
    }

    public function entityClass(): string
    {
        return Task::class;
    }

    /**
     * @return array[]
     */
    public function getColumns(): array
    {
        $columns = [
            'no' => ['task.no.label', true, 'no'],
            'caption' => ['task.caption.label', true, 'caption'],
            'stage' => ['task.stage.label', true],
            'type' => ['task.type.label', true],
            'priority' => ['task.priority.label', true],
            'complexity' => ['task.complexity.label', true],
            'createdAt' => ['task.created.label', true],
            'updatedAt' => ['task.updated.label', true],
        ];
        $settings = $this->project->getTaskSettings();
        if(!$settings->getStages()->isEnabled()) {
            unset($columns['stage']);
        }
        if(!$settings->getTypes()->isEnabled()) {
            unset($columns['type']);
        }
        if(!$settings->getPriority()->isEnabled()) {
            unset($columns['priority']);
        }
        if(!$settings->getComplexity()->isEnabled()) {
            unset($columns['complexity']);
        }
        return $columns;
    }

    public function getDefaultFilterQuery(): FilterQuery
    {
        return (new FilterQuery())
            ->addFilter('project', new ProjectFilter($this->project->getSuffix()))
            ->addFilter('status', new TaskStatusFilter());
    }

    public function getDefaultSort(): SortQuery
    {
        return new SortQuery('updatedAt', SortQuery::DESC);
    }

    /**
     * @return Project
     */
    public function getProject(): Project
    {
        return $this->project;
    }
}