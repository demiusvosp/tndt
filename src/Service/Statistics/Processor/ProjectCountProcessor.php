<?php
/**
 * User: demius
 * Date: 30.07.2024
 * Time: 23:49
 */

namespace App\Service\Statistics\Processor;

use App\Model\Dto\Statistics\ProgressPartItem;
use App\Model\Dto\Statistics\ProgressStatItem;
use App\Model\Enum\StatisticProcessorEnum;
use App\Repository\ProjectRepository;
use App\Service\Statistics\ProcessorInterface;
use Happyr\DoctrineSpecification\Spec;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use function round;

#[AutoconfigureTag("app.statistic.processor",)]
#[AsTaggedItem(index: StatisticProcessorEnum::ProjectCount->value)]
class ProjectCountProcessor implements ProcessorInterface
{
    private const PROJECT_PRECISION = 0;
    private ProjectRepository $projectRepository;

    public function __construct(ProjectRepository $projectRepository)
    {
        $this->projectRepository = $projectRepository;
    }

    public function execute(): ?ProgressStatItem
    {
        $total = (int) $this->projectRepository->matchSingleScalarResult(Spec::countOf(null));
        $archived = (int) $this->projectRepository->matchSingleScalarResult(Spec::countOf(Spec::eq('isArchived', true)));
        return new ProgressStatItem(
            StatisticProcessorEnum::ProjectCount,
            $total,
            [
                new ProgressPartItem(
                    'active',
                    round(100 - $archived / ($total / 100), self::PROJECT_PRECISION)
                )
            ]
        );
    }
}