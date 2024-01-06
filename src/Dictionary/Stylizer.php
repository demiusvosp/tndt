<?php
/**
 * User: demius
 * Date: 08.12.2021
 * Time: 22:34
 */
declare(strict_types=1);

namespace App\Dictionary;

use App\Dictionary\Object\Task\StageTypesEnum;
use App\Dictionary\Object\Task\TaskPriorityItem;
use App\Dictionary\Object\Task\TaskStageItem;
use App\Entity\Contract\HasClosedStatusInterface;
use App\Exception\DictionaryException;
use Psr\Log\LoggerInterface;
use function dump;

class Stylizer
{
    private const LIGHTER_STEP = 35;
    private const INVERSE_THRESHOLD = 127;

    private Fetcher $fetcher;
    private LoggerInterface $logger;

    public function __construct(Fetcher $fetcher, LoggerInterface $logger)
    {
        $this->fetcher = $fetcher;
        $this->logger = $logger;
    }

    public function getStyle($entity, StylesEnum $styleType): string
    {
        try {
            $items = $this->fetcher->getRelatedItems($entity);
        } catch (DictionaryException $e) {
            $this->logger->error("Dictionary stylizer render table for project with incorrect dictionary", ['exception' => $e]);
            return '';
        }

        // @TODO когда появятся больше разных стилизуемых мест и правил, создать систему хендлеров, а пока так.
        if ($styleType->equals(StylesEnum::TASK_ROW())) {
            $style = '';
            $bgColor = [255, 255, 255];
            if (isset($items[TypesEnum::TASK_PRIORITY]) && $items[TypesEnum::TASK_PRIORITY]->isSet()) {
                /** @var TaskPriorityItem $item */
                $item = $items[TypesEnum::TASK_PRIORITY];
                if (!empty($item->getBgColor())) {
                    $bgColor = $this->colorStr2Hex($item->getBgColor());
                }
            }
            if (isset($items[TypesEnum::TASK_STAGE]) && $items[TypesEnum::TASK_STAGE]->isSet()) {
                /** @var TaskStageItem $item */
                $item = $items[TypesEnum::TASK_STAGE];
                // стилизация закрытого состояние не совсем прерогатива справочника, но раз он отвечает за стиль списка
                if (($entity instanceof HasClosedStatusInterface && $entity->isClosed())
                    || $item->getType()->equals(StageTypesEnum::STAGE_ON_CLOSED())
                ) {
                    $bgColor = $this->colorTransform(
                        $bgColor,
                        self::LIGHTER_STEP,
                        self::INVERSE_THRESHOLD
                    );
                }
            }

            if ($bgColor[0] < self::INVERSE_THRESHOLD
                && $bgColor[1] < self::INVERSE_THRESHOLD
                && $bgColor[2] < self::INVERSE_THRESHOLD
            ) {
                $style .= 'color:#fff; ';
            }

            $bgColor = $this->colorHex2Str($bgColor);
            $style .= 'background-color:#' . $bgColor . ';';
            return $style;
        }

        throw new DictionaryException('Невозможно обработать стиль ' . $styleType->getValue());
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

    private function colorTransform(array $color, int $delta, ?int $threshold = null): array
    {
        if ($threshold === null || $threshold < $delta) {
            $threshold = $delta;
        }

        foreach ($color as &$component) {
            if ($component > $threshold) {
                $component -= $delta;
            } else {
                $component += $delta;
            }
        }

        return  $color;
    }
}