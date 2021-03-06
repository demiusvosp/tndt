<?php
/**
 * User: demius
 * Date: 20.11.2021
 * Time: 23:36
 */
declare(strict_types=1);

namespace App\Service\Twig;

use App\Entity\Contract\InProjectInterface;
use App\Dictionary\TypesEnum;
use App\Dictionary\Fetcher;
use App\Dictionary\StylesEnum;
use App\Dictionary\Stylizer;
use App\Exception\DictionaryException;
use App\Service\Badges\BadgeDTO;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class DictionaryExtension extends AbstractExtension
{
    private Fetcher $fetcher;
    private Stylizer $stylizer;
    private TranslatorInterface $translator;
    private BadgesExtension $badgesExtension;

    public function __construct(
        Fetcher             $fetcher,
        Stylizer            $stylizer,
        TranslatorInterface $translator,
        BadgesExtension  $badgesExtension
    ) {
        $this->fetcher = $fetcher;
        $this->stylizer = $stylizer;
        $this->translator = $translator;
        $this->badgesExtension = $badgesExtension;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'dictionary_enabled',
                [$this, 'dictionaryEnabled'],
            ),
            new TwigFunction(
                'dictionary_name',
                [$this, 'dictionaryName'],
                ['is_safe' => ['html']],
            ),
            new TwigFunction(
                'dictionary_style',
                [$this, 'dictionaryStyle']
            )
        ];
    }

    public function dictionaryEnabled(InProjectInterface $projectableEntity, string $dictionaryType): bool
    {
        if (strpos($dictionaryType, '.') !== false) {
            $type = TypesEnum::from($dictionaryType);
        } else {
            $type = TypesEnum::fromEntity($projectableEntity, $dictionaryType);
        }

        $dictionary = $this->fetcher->getDictionary($type, $projectableEntity);

        return $dictionary->isEnabled();
    }

    public function dictionaryName($entity, string $dictionaryType, bool $useBadge = true): string
    {
        if (!$entity instanceof InProjectInterface) {
            throw new DictionaryException('???????????????????? ?????????? ???????????????? ???????????? ???? ???????????????? ?????????????????????? ?? ??????????????');
        }
        $type = TypesEnum::fromEntity($entity, $dictionaryType);
        $item = $this->fetcher->getDictionaryItem($type, $entity);

        if ($useBadge && $item->getUseBadge()) {
            $badge = new BadgeDTO($item->getName(), $item->getUseBadge(), $item->getDescription());
            return $this->badgesExtension->badgeHtml($badge);
        }

        if ($item->getId() === 0) { // ???????????????? ?????????? ?????????????????? ?????????? ?????????????????? TranslatableItem
            $html = '<i class="dictionary-not-set">' . $this->translator->trans($item->getName()) . '</i>';
        } else {
            $html = $item->getName();
        }

        if (!empty($item->getDescription())) {
            $html = '<span title="' . $item->getDescription() . '">' . $html . '</span>';
        }

        return $html;
    }

    public function dictionaryStyle($entity, string $styleType): string
    {
        $style = StylesEnum::fromEntity($entity, $styleType);

        return $this->stylizer->getStyle($entity, $style);
    }
}