<?php
/**
 * User: demius
 * Date: 08.12.2021
 * Time: 22:34
 */
declare(strict_types=1);

namespace App\Service;

use App\Object\Dictionary\DictionaryItem;
use App\Object\Task\TaskComplexity;
use App\Object\Task\TaskPriorityItem;

class DictionaryStylizer
{
    private DictionaryFetcher $fetcher;

    public function __construct(DictionaryFetcher $fetcher)
    {
        $this->fetcher = $fetcher;
    }

    public function getStyle($entity, DictionaryStylesEnum $styleType): string
    {
        $items = $this->fetcher->getRelatedItems($entity);

        // @TODO когда появятся больше разных стилизуемых мест и правил, создать систему хендлеров, а пока так.
        if ($styleType->equals(DictionaryStylesEnum::TASK_ROW())) {
            $style = '';
            $bgColor = [255, 255, 255];
            if (isset($items[DictionariesTypeEnum::TASK_PRIORITY])) {
                /** @var TaskPriorityItem $item */
                $item = $items[DictionariesTypeEnum::TASK_PRIORITY];
                if (!empty($item->getBgColor())) {
                    $bgColor = $this->colorStr2Hex($item->getBgColor());
                }
            }

            $bgColor = $this->colorHex2Str($bgColor);

            $style .= 'background-color:#' . $bgColor . ';';
            return $style;
        }

        throw new \InvalidArgumentException('Невозможно обработать стиль ' . $styleType->getValue());
    }

    private function colorStr2Hex(string $color): array
    {
        $rgbStr = str_split($color, 2);
        return [hexdec($rgbStr[0]), hexdec($rgbStr[1]), hexdec($rgbStr[2])];
    }

    private function colorHex2Str(array $color): string
    {
        return str_pad(dechex($color[0]), 2, '0', STR_PAD_LEFT) .
            str_pad(dechex($color[1]), 2, '0', STR_PAD_LEFT) .
            str_pad(dechex($color[2]), 2, '0', STR_PAD_LEFT);
    }
}