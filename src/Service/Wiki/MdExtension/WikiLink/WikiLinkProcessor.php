<?php
/**
 * User: demius
 * Date: 12.06.2024
 * Time: 15:54
 */

namespace App\Service\Wiki\MdExtension\WikiLink;

use App\Entity\Task;
use App\Service\Wiki\WikiService;
use League\CommonMark\Extension\CommonMark\Node\Inline\Link;
use League\CommonMark\Parser\Inline\InlineParserInterface;
use League\CommonMark\Parser\Inline\InlineParserMatch;
use League\CommonMark\Parser\InlineParserContext;
use function dump;

class WikiLinkProcessor implements InlineParserInterface
{
    private WikiService $wikiService;

    public function __construct(WikiService $wikiService)
    {
        $this->wikiService = $wikiService;
    }

    public function getMatchDefinition(): InlineParserMatch
    {
        return InlineParserMatch::regex('\[(\w+-\d+|\w+#\w+)\]');
    }

    public function parse(InlineParserContext $inlineContext): bool
    {
        $linkTag = $inlineContext->getSubMatches()[0];
        $link = $this->wikiService->getLink($linkTag);
        if ($link) {
            $inlineContext->getCursor()->advanceBy($inlineContext->getFullMatchLength());
            $inlineContext->getContainer()->appendChild(new Link($link->getUrl(), $linkTag, $link->getAlt()));
            return true;
        }
        return false;
    }
}