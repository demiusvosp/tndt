<?php
/**
 * User: demius
 * Date: 17.02.2022
 * Time: 14:08
 */
declare(strict_types=1);

namespace App\Service\Badges;

use App\Entity\Doc;
use App\Model\Dto\Badge;
use App\Model\Enum\BadgeStyleEnum;
use App\Model\Enum\DocStateEnum;
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
     * @return Badge[]
     */
    public function getBadges($doc, array $excepts = []): array
    {
        if (!$doc instanceof Doc) {
            throw new \InvalidArgumentException('Хэндлер возвращает коллекцию баджей для документа, ' . get_class($doc) . ' передан');
        }

        $badges = [];
        if ($doc->getState() === DocStateEnum::Deprecated && !in_array('deprecated', $excepts, true)) {
            $badges[] = new Badge(
                $this->translator->trans('doc.state.deprecated.label'),
                BadgeStyleEnum::Warning,
                $this->translator->trans('doc.state.deprecated.help')
            );
        }
        if ($doc->getState() === DocStateEnum::Archived && !in_array('archived', $excepts, true)) {
            $badges[] = new Badge(
                $this->translator->trans('doc.state.archived.label'),
                BadgeStyleEnum::Secondary,
                $this->translator->trans('doc.state.archived.help')
            );
        }

        return $badges;
    }
}