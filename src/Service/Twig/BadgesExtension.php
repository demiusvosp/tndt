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
        $badgeHtml = '<span class="label label-' . $badge->getStyle()->value . '"';
        if ($badge->getAlt()) {
            $badgeHtml .= ' title="' . $badge->getAlt() . '"';
        }

        $badgeHtml .= '>'
            . $badge->getLabel()
            . '</span>';

        return $badgeHtml;
    }
}