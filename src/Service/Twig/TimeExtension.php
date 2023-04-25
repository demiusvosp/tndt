<?php
/**
 * User: demius
 * Date: 08.09.2021
 * Time: 0:31
 */
declare(strict_types=1);

namespace App\Service\Twig;

use DateTimeInterface;
use Knp\Bundle\TimeBundle\DateTimeFormatter;
use Knp\Bundle\TimeBundle\Twig\Extension\TimeExtension as BaseTimeExtension;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;
use Twig\Error\RuntimeError;
use Twig\Extra\Intl\IntlExtension;
use Twig\TwigFilter;

class TimeExtension extends BaseTimeExtension
{
    protected $translator;
    protected IntlExtension $intlExtension;

    public function __construct(
        DateTimeFormatter $formatter,
        TranslatorInterface $translator,
        IntlExtension $intlExtension)
    {
        parent::__construct($formatter);
        $this->translator = $translator;
        $this->intlExtension = $intlExtension;
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter(
                'ago',
                [$this, 'ago'],
                ['is_safe' => ['html'], 'needs_environment' => true]
            ),
            new TwigFilter(
                'time_diff',
                [$this, 'diff'],
                ['is_safe' => ['html']]
            ),
        ];
    }

    /**
     * @throws RuntimeError
     */
    public function ago(Environment $env, $date = null): string
    {
        if ($date === null) {// нет времени выводим никогда
            return $this->formatHtml($this->translator->trans('time.never'), true);
        }

        $string = $this->formatDiff($this->formatter->getDatetimeObject($date), new \DateTime());
        $italic = false;
        if (!$string) {// нет разницы во времени, выводим сейчас
            $string = $this->translator->trans('time.empty');
            $italic = true;
        }
        return $this->formatHtml($string, $italic, $this->intlExtension->formatDateTime($env, $date));
    }

    /**
     * Returns the name of the extension.
     */
    public function getName(): string
    {
        return 'app_time';
    }

    /**
     * Добавляет html теги, для переводов никогда, сейчас, а так же всплывашку с полной датой
     */
    protected function formatHtml(string $date, bool $italic, ?string $tooltip = null): string
    {
        if ($italic) {
            $tag = 'i';
        } else {
            $tag = 'span';
        }
        if ($tooltip) {
            $tooltip = sprintf(' title="%s"', $tooltip);
        }

        return sprintf(
            '<%s%s>%s</%s>',
            $tag,
            $tooltip,
            $date,
            $tag
        );
    }

    /**
     * Форматирует разницу во времени как месяц и 2 дня
     */
    protected function formatDiff(DateTimeInterface $from, DateTimeInterface $to): ?string
    {
        static $units = array(
            'y' => 'year',
            'm' => 'month',
            'd' => 'day',
            'h' => 'hour',
            'i' => 'minute',
            's' => 'second'
        );

        $diff = $to->diff($from);

        $firstPart = '';
        foreach ($units as $attribute => $unit) {
            $count = $diff->$attribute;

            if ($count === 1 && empty($firstPart)) { // месяц и
                $firstPart = $this->translator->trans(sprintf('time.one_and.%s', $unit)) . ' ';

            } elseif  ($count > 1 || ($count > 0 && !empty($firstPart))) { // 3 месяца либо месяц и 1 день либо месяц и 2 дня
                return $firstPart . $this->formatter->getDiffMessage($count, (bool)$diff->invert, $unit);
            }
        }

        return null;
    }
}