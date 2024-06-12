<?php
/**
 * User: demius
 * Date: 12.06.2024
 * Time: 18:34
 */

namespace App\Service\Wiki;

use App\Model\Dto\WikiLink;
use App\Repository\DocRepository;
use Happyr\DoctrineSpecification\Exception\NoResultException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use function preg_match;

/**
 * В первой итерации назовем так, когда она накопит функционала, продумаем разбиение
 */
class WikiService
{
    private UrlGeneratorInterface $router;
    private DocRepository $docRepository;
    private LoggerInterface $logger;

    public function __construct(UrlGeneratorInterface $router, DocRepository $docRepository, LoggerInterface $logger)
    {
        $this->router = $router;
        $this->docRepository = $docRepository;
        $this->logger = $logger;
    }

    public function getLink(string $linkTag): ?WikiLink
    {
        if (preg_match('/(\w+)-(\d+)/', $linkTag, $matches)) {
            return new WikiLink(
                $this->router->generate('task.index', ['taskId' => $linkTag]),
                ''
            );
        }
        if (preg_match('/(\w+)#(\w+)/', $linkTag, $matches)) {
            try {
                $doc = $this->docRepository->getByDocId($linkTag);
            } catch (NoResultException $e) {
                $this->logger->warning('Document not found', ['docId' => $linkTag]);
                return null;
            }
            return new WikiLink(
                $this->router->generate('doc.index', ['suffix' => $matches[1], 'slug' => $doc->getSlug()]),
                $doc->getCaption()
            );
        }
        return null;
    }
}