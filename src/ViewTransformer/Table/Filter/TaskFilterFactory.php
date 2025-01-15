<?php
/**
 * User: demius
 * Date: 18.12.2024
 * Time: 23:16
 */

namespace App\ViewTransformer\Table\Filter;

use App\Exception\DomainException;
use App\Model\Dto\Table\Filter\TaskStatusFilter;
use App\Model\Dto\Table\TableQuery;
use App\Model\Enum\Table\ProjectTaskTable;
use App\Model\Enum\Table\TableSettingsInterface;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Contracts\Translation\TranslatorInterface;

#[AutoconfigureTag("app.table.filter_factory")]
#[AsTaggedItem(index: ProjectTaskTable::class)]
class TaskFilterFactory implements FilterFactoryInterface
{
    private TaskStatusTransformer $taskStatusTransformer;

    public function __construct(TaskStatusTransformer $taskStatusTransformer)
    {
        $this->taskStatusTransformer = $taskStatusTransformer;
    }

    public function create(TableSettingsInterface $settings, TableQuery $query): array
    {
        if (!$settings instanceof ProjectTaskTable) {
            throw new DomainException("ProjectTaskTable can render only Task filters set");
        }

        return [
            'status' => $this->taskStatusTransformer->transform($query->getFilter()->getFilter('status')),
        ];
    }
}