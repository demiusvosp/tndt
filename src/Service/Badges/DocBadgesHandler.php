<?php
/**
 * User: demius
 * Date: 17.02.2022
 * Time: 14:08
 */
declare(strict_types=1);

namespace App\Service\Badges;

use App\Entity\Doc;
use Symfony\Contracts\Translation\TranslatorInterface;

class DocBadgesHandler implements BadgeHandlerInterface
{
    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @param $entity - support entity
     * @return bool
     */
    public function supports($entity): bool
    {
        return $entity instanceof Doc;
    }

    /**
     * @param Doc $doc
     * @param array $excepts
     * @return BadgeDTO[]
     */
    public function getBadges($doc, array $excepts = []): array
    {
        if (!$doc instanceof Doc) {
            throw new \InvalidArgumentException('Хэндлер возвращает коллекцию баджей для документа, ' . get_class($doc) . ' передан');
        }

        $badges = [];
        if ($doc->getState() === Doc::STATE_DEPRECATED && !in_array('deprecated', $excepts, true)) {
            $badges[] = new BadgeDTO(
                $this->translator->trans('doc.state.deprecated.label'),
                null,
                $this->translator->trans('doc.state.deprecated.help')
            );
        }
        if ($doc->getState() === Doc::STATE_ARCHIVED && !in_array('archived', $excepts, true)) {
            $badges[] = new BadgeDTO(
                $this->translator->trans('doc.state.archive.label'),
                null,
                $this->translator->trans('doc.state.archive.help')
            );
        }

        return $badges;
    }
}