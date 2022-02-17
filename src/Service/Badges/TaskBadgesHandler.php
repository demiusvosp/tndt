<?php
/**
 * User: demius
 * Date: 17.02.2022
 * Time: 14:12
 */
declare(strict_types=1);

namespace App\Service\Badges;

use App\Dictionary\Fetcher;
use App\Dictionary\Object\Task\StageTypesEnum;
use App\Dictionary\Object\Task\TaskStageItem;
use App\Entity\Task;
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
     * @return BadgeDTO[]
     */
    public function getBadges($task, array $excepts = []): array
    {
        if (!$task instanceof Task) {
            throw new \InvalidArgumentException('Хэндлер возвращает коллекцию баджей для задачи, ' . get_class($task) . ' передан');
        }

        $badges = [];
        $dictionaryItems = $this->dictionaryFetcher->getRelatedItems($task);

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
                $badges[] = new BadgeDTO(
                    $label,
                    $itemBadge,
                    $item->getId() > 0 ? $item->getDescription() : null
                );
            }
        }

        return $badges;
    }
}