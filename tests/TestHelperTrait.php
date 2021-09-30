<?php
/**
 * User: demius
 * Date: 01.10.2021
 * Time: 1:38
 */
declare(strict_types=1);

namespace App\Tests;

use PHPUnit\Framework\Constraint\IsEqual;
use PHPUnit\Framework\Constraint\LogicalAnd;
use PHPUnit\Framework\Constraint\LogicalNot;
use Symfony\Component\DomCrawler\Crawler;

trait TestHelperTrait
{

    /**
     * Проверить, что любой селектор из отобранных содержит текст.
     * (например в списке чего-либо присутствует искомый элемент на любой позиции)
     * @param string $text
     * @param Crawler $crawler
     * @param string $message
     */
    public static function assertAnySelectorTextContains(string $text, Crawler $crawler,  string $message = ''): void
    {
        $isContain = false;
        foreach ($crawler->extract(['_text']) as $item) {
            if (mb_strpos($item, $text) !== false) {
                $isContain = true;
                break;
            }
        }

        self::assertTrue($isContain);
    }

    /**
     * Проверить, что ни в одном селекторе из отобранных искомого текста нет.
     * (Например после фильтрации в списке нет элемента, который должен быть отфильтрован)
     * @param string $text
     * @param Crawler $crawler
     * @param string $message
     */
    public static function assertAllSelectorTextNotContains(string $text, Crawler $crawler,  string $message = ''): void
    {
        $isContain = false;
        foreach ($crawler->extract(['_text']) as $item) {
            if (mb_strpos($item, $text) !== false) {
                $isContain = true;
                break;
            }
        }

        self::assertFalse($isContain);
    }
}