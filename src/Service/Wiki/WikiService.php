<?php
/**
 * User: demius
 * Date: 12.06.2024
 * Time: 18:34
 */

namespace App\Service\Wiki;

use App\Entity\Doc;
use App\Entity\Project;
use App\Entity\Task;
use App\Model\Dto\WikiLink;
use App\Model\Enum\Wiki\LinkStyleEnum;
use App\Specification\Doc\ByDocIdSpec;
use App\Specification\Project\ByIdSpec;
use App\Specification\Task\ByTaskIdSpec;
use Doctrine\ORM\EntityManagerInterface;
use Happyr\DoctrineSpecification\Exception\NoResultException;
use Happyr\DoctrineSpecification\Repository\EntitySpecificationRepositoryInterface;
use Happyr\DoctrineSpecification\Spec;
use Happyr\DoctrineSpecification\Specification\Specification;
use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use function implode;
use function preg_match;

/**
 * В первой итерации назовем так, когда она накопит функционала, продумаем разбиение
 */
class WikiService
{
    private UrlGeneratorInterface $router;
    private TranslatorInterface $translator;
    private EntityManagerInterface $entityManager;
    private LoggerInterface $logger;

    private array $entityCache;

    public function __construct(
        UrlGeneratorInterface $router,
        TranslatorInterface $translator,
        EntityManagerInterface $entityManager,
        LoggerInterface $logger
    ) {
        $this->router = $router;
        $this->translator = $translator;
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }

    public function getWikiLinkRegEx()
    {
        return implode('|', [Task::TASKID_REGEX, Doc::DOCID_REGEX, 'p:\w+']);
    }

    public function getLink(string $linkTag): ?WikiLink
    {
        if (preg_match('/(\w+)-(\d+)/', $linkTag, $matches)) {
            return $this->createTaskLink($matches[1], $matches[2]);
        }
        if (preg_match('/(\w+)#(\w+)/', $linkTag, $matches)) {
            return $this->createDocLink($matches[1], $matches[2]);
        }
        if (preg_match('/(\w+):(\w+)/', $linkTag, $matches) && $matches[1] == 'p') {
            return $this->createProjectLink($matches[2]);
        }
        return null;
    }

    private function getEntityByLink(string $linkTag, string $class, Specification $spec): ?object
    {
        $repository = $this->entityManager->getRepository($class);
        if (!$repository instanceof EntitySpecificationRepositoryInterface) {
            throw new InvalidArgumentException('Cannot get EntitySpecificationRepository for "'.$linkTag.'"');
        }
        if (!isset($this->entityCache[$linkTag])) {
            try {
                $this->entityCache[$linkTag] = $repository->matchSingleResult($spec);
            } catch (NoResultException $e) {
                $this->entityCache[$linkTag] = null;
            }
        }
        return $this->entityCache[$linkTag];
    }

    private function createTaskLink(string $suffix, string $taskNo)
    {
        // то вместе с Task::explodeTaskId() и регулярками сверху должно уехать в енам LinkTypes
        $linkTag = $suffix . '-' . $taskNo;

        /** @var Task $task */
        $task = $this->getEntityByLink($linkTag, Task::class, new ByTaskIdSpec($linkTag));
        if (!$task) {
            $this->logger->warning('Task not found', ['link' => $linkTag]);
            return new WikiLink(
                $this->router->generate('task.list', ['suffix' => $suffix]),
                $linkTag,
                LinkStyleEnum::NotFound,
                $this->translator->trans('task.not_found')
            );
        }
        return new WikiLink(
            $this->router->generate('task.index', ['taskId' => $linkTag]),
            $task->getTaskId(),
            $task->isClosed() ? LinkStyleEnum::TaskClosed : LinkStyleEnum::Normal,
            $task->getCaption()
        );
    }

    private function createDocLink(string $suffix, string $docNo): ?WikiLink
    {
        // это вместе с Doc::explodeDocId() и регулярками сверху должно уехать в енам LinkTypes
        $linkTag = $suffix . '#' . $docNo;
        /** @var Doc $doc */
        $doc = $this->getEntityByLink($linkTag, Doc::class, new ByDocIdSpec($linkTag));
        if (!$doc) {
            $this->logger->warning('Document not found', ['link' => $linkTag]);
            return new WikiLink(
                $this->router->generate('doc.project_create', ['suffix' => $suffix]),
                $linkTag,
                LinkStyleEnum::NotFound,
                $this->translator->trans('doc.not_found')
            );
        }
        return new WikiLink(
            $this->router->generate('doc.index', ['suffix' => $suffix, 'slug' => $doc->getSlug()]),
            $doc->getDocId() . ' - ' . $doc->getCaption(),
            $doc->isArchived() ? LinkStyleEnum::DocArchived : LinkStyleEnum::Normal,
            $doc->getCaption()
        );
    }

    private function createProjectLink(string $suffix)
    {
        $linkTag = 'p:' . $suffix;
        /** @var Project $project */
        $project = $this->getEntityByLink($linkTag, Project::class, new ByIdSpec($suffix));
        if (!$project) {
            $this->logger->warning('Project not found', ['link' => $linkTag]);
            return new WikiLink(
                $this->router->generate('project.list'),
                $linkTag,
                LinkStyleEnum::NotFound,
                $this->translator->trans('project.not_found')
            );
        }
        return new WikiLink(
            $this->router->generate('project.index', ['suffix' => $suffix]),
            $project->getSuffix(),
            $project->isArchived() ? LinkStyleEnum::DocArchived : LinkStyleEnum::Normal,
            $project->getName()
        );
    }
}