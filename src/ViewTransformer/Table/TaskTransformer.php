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
use App\Model\Enum\DictionaryTypeEnum;
use App\Model\Enum\Table\ProjectTaskTable;
use App\Service\Dictionary\Fetcher;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag("app.table.model_transformer")]
#[AsTaggedItem(index: ProjectTaskTable::class)]
class TaskTransformer implements ModelTransformerInterface
{
    private Fetcher $dictionaryFetcher;

    public function __construct(Fetcher $dictionaryFetcher)
    {
        $this->dictionaryFetcher = $dictionaryFetcher;
    }

    /**
     * @param Task $row
     * @return array|object
     */
    public function transform(object $model, TableQuery $query): array
    {
        if (!$model instanceof Task) {
            throw new DomainException("ProjectTaskTable can render only Task row");
        }

        $row = [
            'no' => $model->getNo(),
            'caption' => $model->getCaption(),
            'created' => $model->getCreatedAt(),
            'updated' => $model->getUpdatedAt(),
        ];

        if ($query->hasColumn('stage')) {
            $row['stage'] = $this->dictionaryFetcher->getDictionaryItem(DictionaryTypeEnum::TASK_STAGE(), $model);
        }
        if ($query->hasColumn('type')) {
            $row['type'] = $this->dictionaryFetcher->getDictionaryItem(DictionaryTypeEnum::TASK_TYPE(), $model);
        }
        if ($query->hasColumn('priority')) {
            $row['priority'] = $this->dictionaryFetcher->getDictionaryItem(DictionaryTypeEnum::TASK_PRIORITY(), $model);
        }
        if ($query->hasColumn('complexity')) {
            $row['complexity'] = $this->dictionaryFetcher->getDictionaryItem(DictionaryTypeEnum::TASK_COMPLEXITY(), $model);
        }

        return $row;
    }
}