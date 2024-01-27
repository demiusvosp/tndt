<?php
/**
 * User: demius
 * Date: 13.11.2021
 * Time: 2:57
 */
declare(strict_types=1);

namespace App\Form\Type\Project;

use App\Form\DTO\Project\EditTaskSettingsDTO;
use App\Form\Type\Base\DictionaryEditType;
use App\Model\Enum\DictionaryTypeEnum;
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
                    'dictionaryType' => DictionaryTypeEnum::TASK_TYPE(),
                    'label' => 'dictionaries.task_types.label',
                    'help' => 'dictionaries.task_types.help',
                    'required' => false,
                ]
            )
            ->add(
                'stages',
                DictionaryEditType::class,
                [
                    'dictionaryType' => DictionaryTypeEnum::TASK_STAGE(),
                    'label' => 'dictionaries.task_stages.label',
                    'help' => 'dictionaries.task_stages.help',
                    'required' => false,
                ]
            )
            ->add(
                'priority',
                DictionaryEditType::class,
                [
                    'dictionaryType' => DictionaryTypeEnum::TASK_PRIORITY(),
                    'label' => 'dictionaries.task_priority.label',
                    'help' => 'dictionaries.task_priority.help',
                    'required' => false,
                ]
            )
            ->add(
                'complexity',
                DictionaryEditType::class,
                [
                    'dictionaryType' => DictionaryTypeEnum::TASK_COMPLEXITY(),
                    'label' => 'dictionaries.task_complexity.label',
                    'help' => 'dictionaries.task_complexity.help',
                    'required' => false,
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('data_class', EditTaskSettingsDTO::class);
    }
}