<?php
/**
 * User: demius
 * Date: 14.12.2021
 * Time: 22:03
 */
declare(strict_types=1);

namespace App\Service\Twig;

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

    public function __construct(FormFactoryInterface $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'task_close_form',
                [$this, 'closeForm'],
            ),
        ];
    }

    public function closeForm(Task $task): FormView
    {
        $formData = new CloseTaskDTO();
        $closeTaskForm = $this->formFactory->create(CloseTaskForm::class, $formData);

        return $closeTaskForm->createView();
    }
}