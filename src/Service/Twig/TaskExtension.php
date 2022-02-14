<?php
/**
 * User: demius
 * Date: 14.12.2021
 * Time: 22:03
 */
declare(strict_types=1);

namespace App\Service\Twig;

use App\Dictionary\BadgeEnum;
use App\Dictionary\Fetcher;
use App\Dictionary\Object\Task\StageTypesEnum;
use App\Dictionary\Object\Task\TaskStageItem;
use App\Dictionary\TypesEnum;
use App\Entity\Task;
use App\Form\DTO\Task\CloseTaskDTO;
use App\Form\Type\Task\CloseTaskForm;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormView;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TaskExtension extends AbstractExtension
{
    private FormFactoryInterface $formFactory;
    private Fetcher $dictionaryFetcher;

    public function __construct(FormFactoryInterface $formFactory, Fetcher $dictionaryFetcher)
    {
        $this->formFactory = $formFactory;
        $this->dictionaryFetcher = $dictionaryFetcher;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'task_badges',
                [$this, 'badges'],
                ['is_safe' => ['html']]
            ),
            new TwigFunction(
                'task_close_form',
                [$this, 'closeForm'],
            ),

        ];
    }

    public function badges(Task $task, array $excepts = []): string
    {
        $badges = [];
        $dictionaryItems = $this->dictionaryFetcher->getRelatedItems($task);

        foreach ($dictionaryItems as $type => $item) {
            if (in_array($type, $excepts, true)) {
                continue;
            }
            $itemBadge = $item->getUseBadge();
            if ($item instanceof TaskStageItem && $item->getType()->equals(StageTypesEnum::STAGE_ON_CLOSED())) {
                if ($itemBadge === null) {
                    $itemBadge = BadgeEnum::DEFAULT();
                }
            }
            if ($itemBadge) {
                $badges[] = '<span class="label label-' . $itemBadge->getValue() . '">' . $item->getName() . '</span>';
            }
        }

        return implode('', $badges);
    }

    public function closeForm(Task $task): FormView
    {
        $formData = new CloseTaskDTO($task->getSuffix());
        $closeTaskForm = $this->formFactory->create(CloseTaskForm::class, $formData);

        return $closeTaskForm->createView();
    }
}