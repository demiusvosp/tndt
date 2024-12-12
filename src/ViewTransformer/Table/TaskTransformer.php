<?php
/**
 * User: demius
 * Date: 12.12.2024
 * Time: 22:58
 */

namespace App\ViewTransformer\Table;

use App\Entity\Task;
use App\Exception\DomainException;
use App\Model\Enum\DictionaryTypeEnum;
use App\Service\Dictionary\Fetcher;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag("app.table.model_transformer")]
#[AsTaggedItem(index: Task::class)]
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
    public function transform(object $model): array
    {
        if (!$model instanceof Task) {
            throw new DomainException("TaskTable can render only Task row");
        }

        return [
            'no' => $model->getNo(),
            'caption' => $model->getCaption(),
            'stage' => $this->dictionaryFetcher->getDictionaryItem(DictionaryTypeEnum::TASK_STAGE(), $model),
            'type' => $this->dictionaryFetcher->getDictionaryItem(DictionaryTypeEnum::TASK_TYPE(), $model),
            'priority' => $this->dictionaryFetcher->getDictionaryItem(DictionaryTypeEnum::TASK_PRIORITY(), $model),
            'complexity' => $this->dictionaryFetcher->getDictionaryItem(DictionaryTypeEnum::TASK_COMPLEXITY(), $model),
            'created' => $model->getCreatedAt(),
            'updated' => $model->getUpdatedAt(),
        ];
    }
}