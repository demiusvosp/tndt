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
use App\Model\Enum\StatisticItemEnum;
use App\Repository\DocRepository;
use App\Service\Statistics\ProcessorInterface;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag("app.statistic.processor",)]
#[AsTaggedItem(index: StatisticItemEnum::DocCount->value)]
class DocCountProcessor implements ProcessorInterface
{
    private DocRepository $docRepository;

    public function __construct(DocRepository $docRepository)
    {
        $this->docRepository = $docRepository;
    }

    public function execute(): ?PartedStatItem
    {
        $data = $this->docRepository->countsByState(null);
        return new PartedStatItem(
            StatisticItemEnum::DocCount,
            $data['total'],
            [
                new PartItem(
                    'normal',
                    $data[DocStateEnum::Normal->value],
                    'green'
                ),
                new PartItem(
                    'deprecated',
                    $data[DocStateEnum::Deprecated->value],
                    'primary'
                ),
                new PartItem(
                    'archived',
                    $data[DocStateEnum::Archived->value],
                    'orange'
                )
            ]
        );
    }
}