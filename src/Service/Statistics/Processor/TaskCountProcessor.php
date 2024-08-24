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
use Happyr\DoctrineSpecification\Spec;
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
        $total = $this->taskRepository->matchSingleScalarResult(Spec::countOf(null));
        $closed = $this->taskRepository->matchSingleScalarResult(Spec::countOf(Spec::eq('isClosed', true)));
        return new PartedStatItem(
            StatisticItemEnum::TaskCount,
            $total,
            [
                new PartItem(
                    'closed',
                    $closed,
                    'green'
                )
            ]
        );
    }
}