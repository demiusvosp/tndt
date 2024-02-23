<?php
/**
 * User: demius
 * Date: 10.02.2024
 * Time: 1:47
 */

namespace App\Service\Twig;

use Symfony\Component\Asset\Packages;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use function sprintf;
use function str_starts_with;
use function strpos;

class ImageExtension extends AbstractExtension
{
    private Packages $packages;

    public function __construct(Packages $packages)
    {
        $this->packages = $packages;
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
        if (str_contains($icon, '.')) {
            // icon file
            // @deprecated - переходим на спрайт, этот удаляем
            $class = 'icon ' . $class;
            // tabler icon
            return sprintf(
                '<img class="%s" src="%s">',
                $class,
                $this->packages->getUrl('build/icons/' . $icon)
            );
        }
        // tabler svg icon in sprite
        return sprintf(
            '<use xlink:href="%s#tabler-%s" />',
            $this->packages->getUrl('build/icons/tabler-sprite.svg'),
            $icon
        );
    }
}