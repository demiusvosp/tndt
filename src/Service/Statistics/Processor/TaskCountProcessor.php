<?php
/**
 * User: demius
 * Date: 31.07.2024
 * Time: 09:37
 */

namespace App\Service\Statistics\Processor;

use App\Model\Dto\Statistics\ProgressPartItem;
use App\Model\Dto\Statistics\ProgressStatItem;
use App\Model\Enum\StatisticProcessorEnum;
use App\Repository\TaskRepository;
use App\Service\Statistics\ProcessorInterface;
use Happyr\DoctrineSpecification\Spec;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use function round;

#[AutoconfigureTag("app.statistic.processor",)]
#[AsTaggedItem(index: StatisticProcessorEnum::TaskCount->value)]
class TaskCountProcessor implements ProcessorInterface
{
    private const TASK_PRECISION = 1;
    private TaskRepository $taskRepository;

    public function __construct(TaskRepository $taskRepository)
    {
        $this->taskRepository = $taskRepository;
    }

    public function execute(): ?ProgressStatItem
    {
        $total = $this->taskRepository->matchSingleScalarResult(Spec::countOf(null));
        $closed = $this->taskRepository->matchSingleScalarResult(Spec::countOf(Spec::eq('isClosed', true)));
        return new ProgressStatItem(
            StatisticProcessorEnum::TaskCount,
            $total,
            [
                new ProgressPartItem(
                    'closed',
                    round($closed / ($total / 100), self::TASK_PRECISION),
                    'green'
                )
            ]
        );
    }
}