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
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class DictionaryExtension extends AbstractExtension
{
    private Fetcher $fetcher;
    private Stylizer $stylizer;
    private TranslatorInterface $translator;

    public function __construct(
        Fetcher             $fetcher,
        Stylizer            $stylizer,
        TranslatorInterface $translator
    ) {
        $this->fetcher = $fetcher;
        $this->stylizer = $stylizer;
        $this->translator = $translator;
    }

    public function getFunctions(): array
    {
        return [
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

    public function dictionaryName($entity, string $dictionaryType, bool $withAlt = false): string
    {
        if (!$entity instanceof InProjectInterface) {
            throw new \InvalidArgumentException('Справочник можно получить только от сущности относящейся к проекту');
        }
        $dictionary = TypesEnum::fromEntity($entity, $dictionaryType);
        $item = $this->fetcher->getDictionaryItem($dictionary, $entity);

        if ($item->getId() === 0) { // возможно стоит проверять через интерфейс TranslatableItem
            $html = '<i class="dictionary-not-set">' . $this->translator->trans($item->getName()) . '</i>';
        } else {
            $html = $item->getName();
        }

        if ($withAlt) {
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