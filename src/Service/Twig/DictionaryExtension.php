<?php
/**
 * User: demius
 * Date: 20.11.2021
 * Time: 23:36
 */
declare(strict_types=1);

namespace App\Service\Twig;

use App\Entity\Contract\InProjectInterface;
use App\Service\DictionariesTypeEnum;
use App\Service\DictionaryService;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class DictionaryExtension extends AbstractExtension
{
    private DictionaryService $dictionaryService;
    private TranslatorInterface $translator;

    public function __construct(DictionaryService $dictionaryService, TranslatorInterface $translator)
    {
        $this->dictionaryService = $dictionaryService;
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
        ];
    }

    public function dictionaryName($entity, string $dictionaryType, bool $withAlt = false): string
    {
        if (!$entity instanceof InProjectInterface) {
            throw new \InvalidArgumentException('Справочник можно получить только от сущности относящейся к проекту');
        }
        $dictionary = DictionariesTypeEnum::fromEntity($entity, $dictionaryType);
        $item = $this->dictionaryService->getDictionaryItem($dictionary, $entity);

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
}