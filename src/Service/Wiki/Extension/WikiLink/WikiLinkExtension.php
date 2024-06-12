<?php
/**
 * User: demius
 * Date: 12.06.2024
 * Time: 15:38
 */

namespace App\Service\Wiki\Extension\WikiLink;

use League\CommonMark\Environment\EnvironmentBuilderInterface;
use League\CommonMark\Extension\ExtensionInterface;

class WikiLinkExtension implements ExtensionInterface
{
    private WikiLinkProcessor $linkProcessor;

    public function __construct(WikiLinkProcessor $linkProcessor)
    {
        $this->linkProcessor = $linkProcessor;
    }

    public function register(EnvironmentBuilderInterface $environment): void
    {
        $environment->addInlineParser($this->linkProcessor, 100);
    }
}