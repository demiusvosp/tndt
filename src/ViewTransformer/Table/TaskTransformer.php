<?php
/**
 * User: demius
 * Date: 12.12.2024
 * Time: 22:58
 */

namespace App\ViewTransformer\Table;

use App\Entity\Task;
use App\Exception\DomainException;
use App\Model\Dto\Table\TableQuery;
use App\Model\Enum\DictionaryStyleEnum;
use App\Model\Template\Table\ProjectTaskTable;
use App\Service\Dictionary\Fetcher;
use App\Service\Dictionary\Stylizer;
use App\Service\Twig\DictionaryExtension;
use App\ViewModel\Table\Row;
use App\ViewTransformer\TimeTransformer;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\Routing\RouterInterface;
use function sprintf;

#[AutoconfigureTag("app.table.model_transformer")]
#[AsTaggedItem(index: ProjectTaskTable::class)]
class TaskTransformer implements ModelTransformerInterface
{
    private Fetcher $dictionaryFetcher;
    private Stylizer $dictionaryStylizer;
    private RouterInterface $router;
    private DictionaryExtension $dictionaryExtension;
    private TimeTransformer $timeTransformer;

    public function __construct(
        Fetcher $dictionaryFetcher,
        Stylizer $dictionaryStylizer,
        RouterInterface $router,
        DictionaryExtension $dictionaryExtension,
        TimeTransformer $timeTransformer
    ) {
        $this->dictionaryFetcher = $dictionaryFetcher;
        $this->dictionaryStylizer = $dictionaryStylizer;
        $this->router = $router;
        $this->dictionaryExtension = $dictionaryExtension;
        $this->timeTransformer = $timeTransformer;
    }

    public function transform(object $model, TableQuery $query): Row
    {
        if (!$model instanceof Task) {
            throw new DomainException("ProjectTaskTable can render only Task row");
        }

        $row['no'] = $this->taskLink($model->getNo(), $model->getTaskId(), $model->isClosed());
        $row['caption'] = $this->taskLink($model->getCaption(), $model->getTaskId(), $model->isClosed());
        if ($query->hasColumn('stage')) {
            $row['stage'] = $this->dictionaryExtension->dictionaryName($model, 'stage');
        }
        if ($query->hasColumn('type')) {
            $row['type'] = $this->dictionaryExtension->dictionaryName($model, 'type');
        }
        if ($query->hasColumn('priority')) {
            $row['priority'] = $this->dictionaryExtension->dictionaryName($model, 'priority');
        }
        if ($query->hasColumn('complexity')) {
            $row['complexity'] = $this->dictionaryExtension->dictionaryName($model, 'complexity');
        }
        $row['createdAt'] = $this->timeTransformer->ago($model->getCreatedAt());
        $row['updatedAt'] = $this->timeTransformer->ago($model->getUpdatedAt());

        // @todo переделаем в [tndt-188]
        $rowStyle = $this->dictionaryStylizer->getStyle($model, DictionaryStyleEnum::TASK_ROW());
        return new Row($row, $rowStyle);
    }

    private function taskLink(string $text, string $taskId, bool $isClosed): string
    {
        return sprintf(
            '<span class="task%s"><a class="task-link" href="%s">%s</a></span>',
            $isClosed ? ' task-closed' : '',
            $this->router->generate('task.index', ['taskId' => $taskId]),
            $text
        );
    }
}