<?php
/**
 * User: demius
 * Date: 26.08.2021
 * Time: 23:14
 */
declare(strict_types=1);

namespace App\Form\Type\Task;

use App\Form\DTO\Task\EditTaskDTO;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditTaskType extends NewTaskType
{

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('data_class', EditTaskDTO::class);
    }
}