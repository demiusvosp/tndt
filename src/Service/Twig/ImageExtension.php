<?php
/**
 * User: demius
 * Date: 10.02.2024
 * Time: 1:47
 */

namespace App\Service\Twig;

use Psr\Log\LoggerInterface;
use Symfony\Component\Asset\Packages;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use function implode;
use function sprintf;
use function str_starts_with;

class ImageExtension extends AbstractExtension
{
    private Packages $packages;
    private LoggerInterface $logger;

    public function __construct(Packages $packages, LoggerInterface $logger)
    {
        $this->packages = $packages;
        $this->logger = $logger;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'icon',
                [$this, 'icon'],
                ['is_safe' => ['html']]
            ),
        ];
    }

    public function icon(string $icon, string $class = ''): string
    {
        if (str_starts_with($icon, 'fa')) {
            // font-awesome icon
            return sprintf('<span class="%s"><i class="fa-icon %s"></i></span>', $class, $icon);
        }
        // tabler svg icon in sprite
        if (str_starts_with($icon, 'tabler-')) {
            return sprintf(
                '<svg class="%s"><use xlink:href="%s#%s" /></svg>',
                implode(' ', ['icon', $class]),
                $this->packages->getUrl('build/images/tabler-sprite.svg'),
                $icon
            );
        }
        // tndt svg icon in sprite
        if (str_starts_with($icon, 'app-')) {
            return sprintf(
                '<svg class="%s"><use xlink:href="%s#%s" /></svg>',
                implode(' ', ['icon', $class]),
                $this->packages->getUrl('build/images/app-sprite.svg'),
                $icon
            );
        }

        $this->logger->notice('use separate svg icon ' . $icon);
        // icon file
        $class = 'icon ' . $class;
        // tabler icon
        return sprintf(
            '<img class="%s" src="%s">',
            $class,
            $this->packages->getUrl('build/icons/' . $icon)
        );
    }
}