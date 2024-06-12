<?php
/**
 * User: demius
 * Date: 12.06.2024
 * Time: 18:34
 */

namespace App\Service\Wiki;

use App\Entity\Doc;
use App\Entity\Task;
use App\Model\Dto\WikiLink;
use App\Model\Enum\Wiki\LinkStyleEnum;
use App\Repository\DocRepository;
use App\Repository\TaskRepository;
use Happyr\DoctrineSpecification\Exception\NoResultException;
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
    private TaskRepository $taskRepository;
    private DocRepository $docRepository;
    private LoggerInterface $logger;

    private array $entityCache;

    public function __construct(
        UrlGeneratorInterface $router,
        TranslatorInterface $translator,
        TaskRepository $taskRepository,
        DocRepository $docRepository,
        LoggerInterface $logger
    ) {
        $this->router = $router;
        $this->translator = $translator;
        $this->taskRepository = $taskRepository;
        $this->docRepository = $docRepository;
        $this->logger = $logger;
    }

    public function getWikiLinkRegEx()
    {
        return implode('|', [Task::TASKID_REGEX, Doc::DOCID_REGEX]);
    }

    public function getLink(string $linkTag): ?WikiLink
    {
        if (preg_match('/(\w+)-(\d+)/', $linkTag, $matches)) {
            return $this->createTaskLink($linkTag, $matches[1], $matches[2]);
        }
        if (preg_match('/(\w+)#(\w+)/', $linkTag, $matches)) {
            return $this->createDocLink($linkTag, $matches[1], $matches[2]);
        }
        return null;
    }

    private function createTaskLink(string $linkTag, string $suffix, string $taskNo)
    {
        if (!isset($this->entityCache[$linkTag])) {
            $this->entityCache[$linkTag] = $this->taskRepository->findByTaskId($linkTag);
        }
        $task = $this->entityCache[$linkTag];
        if (!$task) {
            $this->logger->warning('Task not found', ['link' => $linkTag]);
            return new WikiLink(
                $this->router->generate('task.list', ['suffix' => $suffix]),
                LinkStyleEnum::NotFound,
                $this->translator->trans('task.not_found')
            );
        }
        return new WikiLink(
            $this->router->generate('task.index', ['taskId' => $linkTag]),
            $task->isClosed() ? LinkStyleEnum::TaskClosed : LinkStyleEnum::Normal,
            $task->getCaption()
        );
    }

    private function createDocLink(string $linkTag, string $suffix, string $docNo): ?WikiLink
    {
        if (!isset($this->entityCache[$linkTag])) {
            $this->entityCache[$linkTag] = $this->docRepository->findByDocId($linkTag);
        }
        $doc = $this->entityCache[$linkTag];
        if (!$doc) {
            $this->logger->warning('Document not found', ['link' => $linkTag]);
            return new WikiLink(
                $this->router->generate('doc.project_create', ['suffix' => $suffix]),
                LinkStyleEnum::NotFound,
                $this->translator->trans('doc.not_found')
            );
        }
        return new WikiLink(
            $this->router->generate('doc.index', ['suffix' => $suffix, 'slug' => $doc->getSlug()]),
            $doc->isArchived() ? LinkStyleEnum::DocArchived : LinkStyleEnum::Normal,
            $doc->getCaption()
        );
    }
}