<?php
/**
 * User: demius
 * Date: 08.09.2021
 * Time: 0:31
 */
declare(strict_types=1);

namespace App\Service\Twig;

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
    public function ago(Environment $env, $date = null, $html = true): string
    {
        $string = $this->diff($date, null, $html);
        if($html) {
            $string = sprintf(
                '<span title="%s">%s</span>',
                $this->intlExtension->formatDateTime($env, $date),
                $string
            );
        }
        return $string;
    }

    public function diff($since = null, $to = null, $html = true): string
    {
        if ($since == null) {
            return '<i>' . $this->translator->trans('never') . '</i>';
        }
        return parent::diff($since, $to);
    }

    /**
     * Returns the name of the extension.
     */
    public function getName(): string
    {
        return 'app_time';
    }
}