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
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditProjectTaskSettingsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            'types',
            DictionaryEditType::class,
            [
                'label' => 'dictionaries.task_types.label',
                'help' => 'dictionaries.task_types.help',
                'required' => false,
            ]
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('data_class', TaskSettings::class);
    }
}