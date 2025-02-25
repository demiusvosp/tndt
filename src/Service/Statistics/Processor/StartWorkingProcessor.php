<?php
/**
 * User: demius
 * Date: 30.07.2024
 * Time: 11:03
 */

namespace App\Service\Statistics\Processor;

use App\Model\Dto\Statistics\DateTimeStatItem;
use App\Model\Enum\StatisticItemEnum;
use App\Repository\ProjectRepository;
use App\Service\Statistics\ProcessorInterface;
use DateTimeImmutable;
use Doctrine\ORM\NoResultException;
use Happyr\DoctrineSpecification\Spec;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag("app.statistic.processor",)]
#[AsTaggedItem(index: StatisticItemEnum::StartWorking->value)]
class StartWorkingProcessor implements ProcessorInterface
{
    private ProjectRepository $projectRepository;

    public function __construct(ProjectRepository $projectRepository)
    {
        $this->projectRepository = $projectRepository;
    }

    public function execute(): ?DateTimeStatItem
    {
        try {
            $earlierProjectDate = $this->projectRepository->matchSingleScalarResult(
                Spec::andX(
                    Spec::select('createdAt'),
                    Spec::orderBy('createdAt', 'ASC'),
                    Spec::limit(1)
                ));
        } catch (NoResultException $e) {
            return null;
        }

        return new DateTimeStatItem(
            StatisticItemEnum::StartWorking,
            new DateTimeImmutable($earlierProjectDate)
        );
    }
}