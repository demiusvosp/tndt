<?php
/**
 * User: demius
 * Date: 30.07.2024
 * Time: 23:49
 */

namespace App\Service\Statistics\Processor;

use App\Model\Dto\Statistics\PartItem;
use App\Model\Dto\Statistics\PartedStatItem;
use App\Model\Enum\StatisticProcessorEnum;
use App\Repository\ProjectRepository;
use App\Service\Statistics\ProcessorInterface;
use Happyr\DoctrineSpecification\Spec;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag("app.statistic.processor",)]
#[AsTaggedItem(index: StatisticProcessorEnum::ProjectCount->value)]
class ProjectCountProcessor implements ProcessorInterface
{
    private ProjectRepository $projectRepository;

    public function __construct(ProjectRepository $projectRepository)
    {
        $this->projectRepository = $projectRepository;
    }

    public function execute(): ?PartedStatItem
    {
        $total = (int) $this->projectRepository->matchSingleScalarResult(Spec::countOf(null));
        $active = (int) $this->projectRepository->matchSingleScalarResult(Spec::countOf(Spec::eq('isArchived', false)));
        return new PartedStatItem(
            StatisticProcessorEnum::ProjectCount,
            $total,
            [
                new PartItem(
                    'active',
                    $active,
                    'blue'
                )
            ]
        );
    }
}