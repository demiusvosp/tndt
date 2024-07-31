<?php
/**
 * User: demius
 * Date: 30.07.2024
 * Time: 23:39
 */

namespace App\Service\Statistics\Processor;

use App\Model\Dto\Statistics\SingleCountStatItem;
use App\Model\Dto\Statistics\StatItemInterface;
use App\Model\Enum\StatisticProcessorEnum;
use App\Repository\ActivityRepository;
use App\Service\Statistics\ProcessorInterface;
use Happyr\DoctrineSpecification\Spec;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag("app.statistic.processor",)]
#[AsTaggedItem(index: StatisticProcessorEnum::ActivityCount->value)]
class ActivityCountProcessor implements ProcessorInterface
{
    private ActivityRepository $commentRepository;

    public function __construct(ActivityRepository $commentRepository)
    {
        $this->commentRepository = $commentRepository;
    }

    public function execute(): StatItemInterface
    {
        $count = $this->commentRepository->matchSingleScalarResult(
            Spec::countOf(null)
        );
        return new SingleCountStatItem(StatisticProcessorEnum::CommentCount, $count);
    }
}