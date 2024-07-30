<?php
/**
 * User: demius
 * Date: 30.07.2024
 * Time: 11:03
 */

namespace App\Service\Statistics\Processor;

use App\Model\Dto\Statistics\DateTimeStatItem;
use App\Model\Enum\StatisticProcessorEnum;
use App\Repository\ProjectRepository;
use App\Service\Statistics\ProcessorInterface;
use DateTimeImmutable;
use Doctrine\ORM\NoResultException;
use Happyr\DoctrineSpecification\Spec;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use function dump;

#[AutoconfigureTag("app.statistic.processor",)]
#[AsTaggedItem(index: StatisticProcessorEnum::FromStartWorking->value)]
class FromStartWorkingProcessor implements ProcessorInterface
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
            StatisticProcessorEnum::FromStartWorking,
            new DateTimeImmutable($earlierProjectDate)
        );
    }
}