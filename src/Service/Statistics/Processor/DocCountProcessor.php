<?php
/**
 * User: demius
 * Date: 31.07.2024
 * Time: 11:15
 */

namespace App\Service\Statistics\Processor;

use App\Model\Dto\Statistics\PartItem;
use App\Model\Dto\Statistics\PartedStatItem;
use App\Model\Enum\DocStateEnum;
use App\Model\Enum\StatisticProcessorEnum;
use App\Repository\DocRepository;
use App\Service\Statistics\ProcessorInterface;
use Happyr\DoctrineSpecification\Spec;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag("app.statistic.processor",)]
#[AsTaggedItem(index: StatisticProcessorEnum::DocCount->value)]
class DocCountProcessor implements ProcessorInterface
{
    private DocRepository $docRepository;

    public function __construct(DocRepository $docRepository)
    {
        $this->docRepository = $docRepository;
    }

    public function execute(): ?PartedStatItem
    {
        $total = $this->docRepository->matchSingleScalarResult(Spec::countOf(null));
        $deprecated = $this->docRepository->matchSingleScalarResult(
            Spec::countOf(Spec::eq('state', DocStateEnum::Deprecated->value))
        );
        $archived = $this->docRepository->matchSingleScalarResult(
            Spec::countOf(Spec::eq('state', DocStateEnum::Archived->value))
        );

        return new PartedStatItem(
            StatisticProcessorEnum::TaskCount,
            $total,
            [
                new PartItem(
                    'normal',
                    $total - $deprecated - $archived,
                    'green'
                ),
                new PartItem(
                    'deprecated',
                    $deprecated,
                    'primary'
                ),
                new PartItem(
                    'archived',
                    $archived,
                    'orange'
                )
            ]
        );
    }
}