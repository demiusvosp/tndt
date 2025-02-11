<?php
/**
 * User: demius
 * Date: 12.06.2024
 * Time: 15:54
 */

namespace App\Service\Wiki\MdExtension\WikiLink;

use App\Model\Enum\Wiki\LinkStyleEnum;
use App\Service\Wiki\WikiService;
use League\CommonMark\Extension\CommonMark\Node\Inline\Link;
use League\CommonMark\Parser\Inline\InlineParserInterface;
use League\CommonMark\Parser\Inline\InlineParserMatch;
use League\CommonMark\Parser\InlineParserContext;
use Symfony\Component\Stopwatch\Stopwatch;
use function implode;

class WikiLinkProcessor implements InlineParserInterface
{
    private WikiService $wikiService;
    private Stopwatch $stopwatch;

    public function __construct(WikiService $wikiService, Stopwatch $stopwatch)
    {
        $this->wikiService = $wikiService;
        $this->stopwatch = $stopwatch;
    }

    public function getMatchDefinition(): InlineParserMatch
    {
        $tagRegex = implode('', [
            '\[(',
            $this->wikiService->getWikiLinkRegEx(),
            ')\][^(].'
        ]);
        return InlineParserMatch::regex($tagRegex);
    }

    public function parse(InlineParserContext $inlineContext): bool
    {
        $this->stopwatch->start('link', 'wiki.parser');
        $linkTag = $inlineContext->getSubMatches()[0];
        $wikiLink = $this->wikiService->getLink($linkTag);
        if ($wikiLink) {
            $inlineContext->getCursor()->advanceBy($inlineContext->getFullMatchLength());

            $link = new Link($wikiLink->getUrl(), $wikiLink->getTitle(), $wikiLink->getAlt());
            if ($wikiLink->getStyle() != LinkStyleEnum::Normal) {
                $link->data->set('attributes/class', $wikiLink->getStyle()->getCssClass());
            }
            $inlineContext->getContainer()->appendChild($link);
        }
        $this->stopwatch->stop('link');
        return $wikiLink != null;
    }
}