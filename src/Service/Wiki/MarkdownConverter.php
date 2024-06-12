<?php
/**
 * User: demius
 * Date: 12.06.2024
 * Time: 13:34
 */

namespace App\Service\Wiki;

use App\Exception\WikiConvertException;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Exception\CommonMarkException;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use Twig\Extra\Markdown\MarkdownInterface;
use League\CommonMark\MarkdownConverter as LeagueCommonMarkConverter;
use function dump;
use function get_class;

class MarkdownConverter implements MarkdownInterface
{
    private LeagueCommonMarkConverter $converter;

    public function __construct(array $config = [], iterable $extensions = [])
    {
        $environment = new Environment($config);
        $this->configureEnvironment($environment, $extensions);

        $this->converter = new LeagueCommonMarkConverter($environment);
    }

    public function convert(string $body): string
    {
        try {
            return $this->converter->convert($body);
        } catch (CommonMarkException $e) {
            throw new WikiConvertException($e);
        }
    }

    private function configureEnvironment(Environment $environment, iterable $extensions): void
    {
        foreach ($extensions as $extension) {
            $environment->addExtension($extension);
        }
    }
}