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
use Twig\TwigFilter;

class TimeExtension extends BaseTimeExtension
{
    protected $translator;

    public function __construct(DateTimeFormatter $formatter, TranslatorInterface $translator)
    {
        parent::__construct($formatter);
        $this->translator = $translator;
    }

    public function getFilters(): array
    {
        return array(
            new TwigFilter(
                'ago',
                array($this, 'diff'),
                array('is_safe' => array('html'))
            ),
        );
    }

    public function diff($since = null, $to = null, $locale = null): string
    {
        if ($since == null) {
            return $this->translator->trans('never');
        }
        return parent::diff($since, $to, $locale);
    }

    /**
     * Returns the name of the extension.
     */
    public function getName(): string
    {
        return 'app_time';
    }
}