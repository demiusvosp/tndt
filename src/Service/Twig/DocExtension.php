<?php
/**
 * User: demius
 * Date: 13.02.2022
 * Time: 1:56
 */
declare(strict_types=1);

namespace App\Service\Twig;

use App\Entity\Doc;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class DocExtension extends AbstractExtension
{
    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction(
                'doc_badges',
                [$this, 'badges'],
                ['is_safe' => ['html']]
            ),
        ];
    }

    public function badges(Doc $doc): string
    {
        $badges = [];
        if ($doc->getState() === Doc::STATE_DEPRECATED) {
            $badges[] = '<span class="label label-default">' . $this->translator->trans('doc.state.deprecated.label') . '</span>';
        }
        if ($doc->getState() === Doc::STATE_ARCHIVED) {
            $badges[] = '<span class="label label-default">' . $this->translator->trans('doc.state.archive.label') . '</span>';
        }

        return implode('', $badges);
    }
}