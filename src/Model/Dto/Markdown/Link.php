<?php
/**
 * User: demius
 * Date: 12.06.2024
 * Time: 19:13
 */

namespace App\Model\Dto\Markdown;

use App\Model\Enum\Wiki\LinkStyleEnum;
use League\CommonMark\Extension\CommonMark\Node\Inline\Link as CommonmarkLink;
class Link extends CommonmarkLink
{
    private LinkStyleEnum $style;

    public function __construct(string $url, ?string $label = null, ?string $title = null, LinkStyleEnum $style = LinkStyleEnum::Normal)
    {
        parent::__construct($url, $label, $title);
        $this->style = $style;
    }

    public function getStyle(): LinkStyleEnum
    {
        return $this->style;
    }
}