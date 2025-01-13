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
use App\ViewModel\Table\Filter\StageFilter;
use App\ViewModel\Table\Filter\ChecksFilter;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Contracts\Translation\TranslatorInterface;

#[AutoconfigureTag("app.table.filter_factory")]
#[AsTaggedItem(index: ProjectTaskTable::class)]
class TaskFilterFactory implements FilterFactoryInterface
{
    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function create(TableSettingsInterface $settings, TableQuery $query): array
    {
        if (!$settings instanceof ProjectTaskTable) {
            throw new DomainException("ProjectTaskTable can render only Task filters set");
        }

        /** @var TaskStatusFilter $queryFilter */
        $queryFilter = $query->getFilter()->getFilter('status');
        return [
            'status' => [
                'name' => 'status',
                'label' => $this->translator->trans('task.isClosed.label'),
                'options' => [
                    [
                        'label' => $this->translator->trans('task.open.label'),
                        'value' => TaskStatusFilter::OPEN,
                        'checked' => $queryFilter->isSelected(TaskStatusFilter::OPEN)
                    ],
                    [
                        'label' => $this->translator->trans('task.close.label'),
                        'value' => TaskStatusFilter::CLOSE,
                        'checked' => $queryFilter->isSelected(TaskStatusFilter::CLOSE)
                    ],
                ],
            ],
        ];
    }
}