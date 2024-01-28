<?php
/**
 * User: demius
 * Date: 17.02.2022
 * Time: 14:12
 */
declare(strict_types=1);

namespace App\Service\Badges;

use App\Entity\Task;
use App\Exception\DictionaryException;
use App\Model\Dto\Badge;
use App\Model\Dto\Dictionary\Task\TaskStageItem;
use App\Model\Enum\BadgeEnum;
use App\Service\Dictionary\Fetcher;
use Symfony\Contracts\Translation\TranslatorInterface;

class TaskBadgesHandler implements BadgeHandlerInterface
{
    private TranslatorInterface $translator;
    private Fetcher $dictionaryFetcher;

    public function __construct(TranslatorInterface $translator, Fetcher $dictionaryFetcher)
    {
        $this->translator = $translator;
        $this->dictionaryFetcher = $dictionaryFetcher;
    }

    /**
     * @param $entity - support entity
     * @return bool
     */
    public function supports($entity): bool
    {
        return $entity instanceof Task;
    }

    /**
     * @param Task $task
     * @param array $excepts
     * @return Badge[]
     */
    public function getBadges($task, array $excepts = []): array
    {
        if (!$task instanceof Task) {
            throw new \InvalidArgumentException('Хэндлер возвращает коллекцию баджей для задачи, ' . get_class($task) . ' передан');
        }

        $badges = [];
        try {
            $dictionaryItems = $this->dictionaryFetcher->getRelatedItems($task);
        } catch (DictionaryException $e) {
            return [new Badge(
                $this->translator->trans('dictionaries.error.name'),
                BadgeEnum::Warning,
                $this->translator->trans('dictionaries.error.description')
            )];
        }

        foreach ($dictionaryItems as $type => $item) {
            if (in_array($type, $excepts, true)) {
                continue;
            }
            $label = null;
            $itemBadge = $item->getUseBadge();
            if ($item instanceof TaskStageItem && !$item->getId() && $task->isClosed()) {
                // если справочника этапа нет, а задача закрыта, создаем бадж об этом состоянии
                $label = $this->translator->trans('task.close.label');

            } elseif ($itemBadge) {
                $label = $item->getName();
            }

            if ($label) {
                $badges[] = new Badge(
                    $label,
                    $itemBadge,
                    $item->getId() > 0 ? $item->getDescription() : null
                );
            }
        }

        return $badges;
    }
}