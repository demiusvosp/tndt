<?php
/**
 * User: demius
 * Date: 30.07.2024
 * Time: 11:20
 */

namespace App\Service\Statistics\Processor;

use App\Model\Dto\Statistics\SingleCountStatItem;
use App\Model\Dto\Statistics\StatItemInterface;
use App\Model\Enum\StatisticItemEnum;
use App\Repository\CommentRepository;
use App\Service\Statistics\ProcessorInterface;
use Happyr\DoctrineSpecification\Spec;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag("app.statistic.processor",)]
#[AsTaggedItem(index: StatisticItemEnum::CommentCount->value)]
class CommentCountProcessor implements ProcessorInterface
{
    private CommentRepository $commentRepository;

    public function __construct(CommentRepository $commentRepository)
    {
        $this->commentRepository = $commentRepository;
    }

    public function execute(): StatItemInterface
    {
        $count = $this->commentRepository->matchSingleScalarResult(
            Spec::countOf(null)
        );
        return new SingleCountStatItem(StatisticItemEnum::CommentCount, $count);
    }
}