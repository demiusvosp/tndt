<?php
/**
 * User: demius
 * Date: 31.07.2024
 * Time: 11:15
 */

namespace App\Service\Statistics\Processor;

use App\Model\Dto\Statistics\ProgressPartItem;
use App\Model\Dto\Statistics\ProgressStatItem;
use App\Model\Dto\Statistics\StatItemInterface;
use App\Model\Enum\DocStateEnum;
use App\Model\Enum\StatisticProcessorEnum;
use App\Repository\DocRepository;
use App\Repository\TaskRepository;
use App\Service\Statistics\ProcessorInterface;
use Happyr\DoctrineSpecification\Spec;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use function round;

#[AutoconfigureTag("app.statistic.processor",)]
#[AsTaggedItem(index: StatisticProcessorEnum::DocCount->value)]
class DocCountProcessor implements ProcessorInterface
{
    private const DOC_PRECISION = 1;
    private DocRepository $docRepository;

    public function __construct(DocRepository $docRepository)
    {
        $this->docRepository = $docRepository;
    }

    public function execute(): ?ProgressStatItem
    {
        $total = $this->docRepository->matchSingleScalarResult(Spec::countOf(null));
        $deprecated = $this->docRepository->matchSingleScalarResult(
            Spec::countOf(Spec::eq('state', DocStateEnum::Deprecated->value))
        );
        $archived = $this->docRepository->matchSingleScalarResult(
            Spec::countOf(Spec::eq('state', DocStateEnum::Archived->value))
        );

        return new ProgressStatItem(
            StatisticProcessorEnum::TaskCount,
            $total,
            [
                new ProgressPartItem(
                    'normal',
                    round(($total - $deprecated - $archived) / ($total / 100), self::DOC_PRECISION),
                    'green'
                ),
                new ProgressPartItem(
                    'deprecated',
                    round($deprecated / ($total / 100), self::DOC_PRECISION),
                    'primary'
                ),
                new ProgressPartItem(
                    'archived',
                    round($archived / ($total / 100), self::DOC_PRECISION),
                    'orange'
                )
            ]
        );
    }
}