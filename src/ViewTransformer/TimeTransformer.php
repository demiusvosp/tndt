<?php
/**
 * User: demius
 * Date: 20.02.2024
 * Time: 0:14
 */

namespace App\ViewTransformer;

use App\Service\Twig\TimeExtension;
use DateTime;
use Twig\Environment;

class TimeTransformer
{
    private Environment $twigEnvironment;
    private TimeExtension $timeTwigExtension;

    public function __construct(Environment $environment, TimeExtension $timeExtension)
    {
        $this->twigEnvironment = $environment;
        $this->timeTwigExtension = $timeExtension;
    }

    /**
     * @throws \Twig\Error\RuntimeError
     */
    public function ago(DateTime $dateTime): string
    {
        return $this->timeTwigExtension->ago($this->twigEnvironment, $dateTime);
    }
}