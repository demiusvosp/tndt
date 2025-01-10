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
//        $projectSettings = $settings->getProject()->getTaskSettings();

        $filters['status'] = new ChecksFilter(
            'status',
            $this->translator->trans('task.isClosed.label'),
        );
        // В первой итерации делаем максимально просто
        // @todo in [tndt-190]
//        if ($projectSettings->getStages()->isEnabled()) {
//            foreach ($projectSettings->getStages()->getItems() as $stage) {
//                $filters['stage']->addItem($stage->getName(), $stage->getId());
//            }
//        } else {
        /** @var TaskStatusFilter $queryFilter */
        $queryFilter = $query->getFilter()->getFilter('status');
        $filters['status']
            ->addItem(
                $this->translator->trans('task.open.label'),
                TaskStatusFilter::OPEN,
                $queryFilter->isSelected(TaskStatusFilter::OPEN)
            )
            ->addItem(
                $this->translator->trans('task.close.label'),
                TaskStatusFilter::CLOSE,
                $queryFilter->isSelected(TaskStatusFilter::CLOSE)
            );

        return $filters;
    }
}