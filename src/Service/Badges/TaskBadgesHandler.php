<?php
/**
 * User: demius
 * Date: 17.02.2022
 * Time: 14:12
 */
declare(strict_types=1);

namespace App\Service\Badges;

use App\Dictionary\BadgeEnum;
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
            $style = $label = null;
            $itemBadge = $item->getUseBadge();
            if ($item instanceof TaskStageItem) {
                // особое поведение этапов задачи
                if ($item->getType()->equals(StageTypesEnum::STAGE_ON_CLOSED())) {
                    // всегда отображаем бадж закрытого этапа
                    $label = $item->getName();
                    if ($itemBadge === null) {
                        //стилизуя по умолчанию, если не настроена кастомная стилизация
                        $style = BadgeEnum::DEFAULT()->getValue();
                    } else {
                        $style = $itemBadge->getValue();
                    }
                }

                if (!$style && $task->isClosed()) {
                    // если задача акрыта, а справочника нет, создаем бадж по состоянию задачи
                    $style = BadgeEnum::DEFAULT()->getValue();
                    $label = $this->translator->trans('task.close.label');
                }

            } elseif ($itemBadge) {
                $label = $item->getName();
                $style = $itemBadge->getValue();
            }

            if ($style) {
                $badges[] = new BadgeDTO(
                    $style,
                    $label,
                    $item->getId() > 0 ? $item->getDescription() : null
                );
            }
        }

        return $badges;
    }
}