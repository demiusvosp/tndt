<?php
/**
 * User: demius
 * Date: 13.08.2021
 * Time: 18:29
 */
declare(strict_types=1);

namespace App\Form\Type\Task;

use App\Dictionary\TypesEnum;
use App\Form\DTO\Task\NewTaskDTO;
use App\Form\Type\Base\DictionarySelectType;
use App\Form\Type\Base\DictionaryStageSelectType;
use App\Form\Type\User\UserSelectType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NewTaskType extends EditTaskType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $this->buildCommonFields($builder);
        $this->buildDictionaryFields($builder, DictionaryStageSelectType::SCENARIO_NEW);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('data_class', NewTaskDTO::class);
    }
}