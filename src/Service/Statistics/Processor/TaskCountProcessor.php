<?php
/**
 * User: demius
 * Date: 31.07.2024
 * Time: 09:37
 */

namespace App\Service\Statistics\Processor;

use App\Model\Dto\Statistics\PartItem;
use App\Model\Dto\Statistics\PartedStatItem;
use App\Model\Enum\StatisticItemEnum;
use App\Repository\TaskRepository;
use App\Service\Statistics\ProcessorInterface;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag("app.statistic.processor",)]
#[AsTaggedItem(index: StatisticItemEnum::TaskCount->value)]
class TaskCountProcessor implements ProcessorInterface
{
    private TaskRepository $taskRepository;

    public function __construct(TaskRepository $taskRepository)
    {
        $this->taskRepository = $taskRepository;
    }

    public function execute(): ?PartedStatItem
    {
        $data = $this->taskRepository->countByClosed();

        return new PartedStatItem(
            StatisticItemEnum::TaskCount,
            $data[false] + $data[true],
            [
                new PartItem(
                    'closed',
                    $data[true],
                    'green'
                )
            ]
        );
    }
}