<?php
/**
 * User: demius
 * Date: 13.11.2021
 * Time: 2:57
 */
declare(strict_types=1);

namespace App\Form\Type\Project;

use App\Form\Type\Base\DictionaryEditType;
use App\Object\Project\TaskSettings;
use App\Service\DictionariesTypeEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditProjectTaskSettingsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'types',
                DictionaryEditType::class,
                [
                    'dictionaryType' => DictionariesTypeEnum::TASK_TYPE(),
                    'label' => 'dictionaries.task_types.label',
                    'help' => 'dictionaries.task_types.help',
                    'required' => false,
                ]
            )
            ->add(
                'complexity',
                DictionaryEditType::class,
                [
                    'dictionaryType' => DictionariesTypeEnum::TASK_COMPLEXITY(),
                    'label' => 'dictionaries.task_complexity.label',
                    'help' => 'dictionaries.task_complexity.help',
                    'required' => false,
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('data_class', TaskSettings::class);
    }
}