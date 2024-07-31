<?php
/**
 * User: demius
 * Date: 31.07.2024
 * Time: 09:37
 */

namespace App\Service\Statistics\Processor;

use App\Model\Dto\Statistics\ProgressStatItem;
use App\Model\Enum\StatisticProcessorEnum;
use App\Repository\TaskRepository;
use App\Service\Statistics\ProcessorInterface;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag("app.statistic.processor",)]
#[AsTaggedItem(index: StatisticProcessorEnum::TaskCount->value)]
class TaskCountProcessor implements ProcessorInterface
{
    private TaskRepository $taskRepository;

    public function __construct(TaskRepository $taskRepository)
    {
        $this->taskRepository = $taskRepository;
    }

    public function execute(): ?ProgressStatItem
    {
        return new ProgressStatItem(
            StatisticProcessorEnum::TaskCount,
            0,
            []
        );
    }
}