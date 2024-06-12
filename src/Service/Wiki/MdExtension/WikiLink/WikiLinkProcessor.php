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
use function implode;

class WikiLinkProcessor implements InlineParserInterface
{
    private WikiService $wikiService;

    public function __construct(WikiService $wikiService)
    {
        $this->wikiService = $wikiService;
    }

    public function getMatchDefinition(): InlineParserMatch
    {
        $tagRegex = implode('', [
            '\[(',
            $this->wikiService->getWikiLinkRegEx(),
            ')\]'
        ]);
        return InlineParserMatch::regex($tagRegex);
    }

    public function parse(InlineParserContext $inlineContext): bool
    {
        $linkTag = $inlineContext->getSubMatches()[0];
        $wikiLink = $this->wikiService->getLink($linkTag);
        if ($wikiLink) {
            $inlineContext->getCursor()->advanceBy($inlineContext->getFullMatchLength());

            $link = new Link($wikiLink->getUrl(), $linkTag, $wikiLink->getAlt());
            if ($wikiLink->getStyle() != LinkStyleEnum::Normal) {
                $link->data->set('attributes/class', $wikiLink->getStyle()->getCssClass());
            }
            $inlineContext->getContainer()->appendChild($link);
            return true;
        }
        return false;
    }
}