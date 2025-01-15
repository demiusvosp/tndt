<?php
/**
 * User: demius
 * Date: 14.01.2025
 * Time: 20:52
 */

namespace App\ViewTransformer\Table\Filter;

use App\Exception\DomainException;
use App\Model\Dto\Table\Filter\FilterInterface;
use App\Model\Dto\Table\Filter\TaskStatusFilter;
use Symfony\Contracts\Translation\TranslatorInterface;

class TaskStatusTransformer
{
    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function transform(FilterInterface $queryFilter)
    {
        if (! $queryFilter instanceof TaskStatusFilter) {
            throw new DomainException(self::class . ' accepts only TaskStatusFilter');
        }
        return [
            'name' => 'status',
            'label' => $this->translator->trans('task.isClosed.label'),
            'options' => [
                [
                    'label' => $this->translator->trans('task.open.label'),
                    'value' => TaskStatusFilter::OPEN,
                    'checked' => $queryFilter->isSelected(TaskStatusFilter::OPEN)
                ],
                [
                    'label' => $this->translator->trans('task.close.label'),
                    'value' => TaskStatusFilter::CLOSE,
                    'checked' => $queryFilter->isSelected(TaskStatusFilter::CLOSE)
                ],
            ],
        ];
    }
}