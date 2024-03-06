<?php
/**
 * User: demius
 * Date: 17.02.2022
 * Time: 13:32
 */
declare(strict_types=1);

namespace App\Service\Twig;

use App\Model\Dto\Badge;
use App\Service\Badges\BadgeHandlerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use function implode;
use function sprintf;

class BadgesExtension extends AbstractExtension
{
    /**
     * @var BadgeHandlerInterface[]
     */
    private iterable $badgesHandlers;

    public function __construct(iterable $badgesHandlers)
    {
        $this->badgesHandlers = $badgesHandlers;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'badges',
                [$this, 'badges'],
                ['is_safe' => ['html']]
            ),
        ];
    }

    public function badges($entity, array $excepts = []): string
    {
        $html = [];

        foreach ($this->badgesHandlers as $handler) {
            if ($handler->supports($entity)) {
                foreach ($handler->getBadges($entity, $excepts) as $badgeItem) {
                    $html[] = $this->badgeHtml($badgeItem);
                }
            }
        }

        return implode('', $html);
    }

    public function badgeHtml(Badge $badge): string
    {
        $class = [
            'badge',
            'bg-' . $badge->getStyle()->value . '-lt',
//            'text-' . $badge->getStyle()->value . '-fg'
        ];

        return sprintf(
            '<span class="%s"%s>%s</span>',
            implode(' ', $class),
            $badge->getAlt() ? ' title="' . $badge->getAlt() . '"' : '',
            $badge->getLabel()
        );
    }
}