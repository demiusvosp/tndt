<?php
/**
 * User: demius
 * Date: 26.08.2021
 * Time: 23:14
 */
declare(strict_types=1);

namespace App\Form\Type\Task;

use App\Form\DTO\Task\EditTaskDTO;
use App\Form\Type\Base\DictionaryStageSelectType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditTaskType extends NewTaskType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $this->buildCommonFields($builder);
        $this->buildDictionaryFields($builder);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('data_class', EditTaskDTO::class);
    }

}