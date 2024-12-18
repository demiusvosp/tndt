<?php
/**
 * User: demius
 * Date: 18.12.2024
 * Time: 23:16
 */

namespace App\ViewTransformer\Table\Filter;

use App\Exception\DomainException;
use App\Model\Dto\Table\TableQuery;
use App\Model\Enum\Table\ProjectTaskTable;
use App\Model\Enum\Table\TableSettingsInterface;
use App\ViewModel\Table\Filter\StageFilter;
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
        $projectSettings = $settings->getProject()->getTaskSettings();

        $filters['stage'] = new StageFilter(
            $this->translator->trans('dictionaries.task_stages.label')
        );
        if ($projectSettings->getStages()->isEnabled()) {
            foreach ($projectSettings->getStages()->getItems() as $stage) {
                $filters['stage']->addOption($stage->getName(), $stage->getId());
            }
        } else {
            $filters['stage']
                ->addOption($this->translator->trans('task.open.label'), 0)
                ->addOption($this->translator->trans('task.close.label'), 1);
        }

        return $filters;
    }
}