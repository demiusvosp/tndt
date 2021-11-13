<?php
/**
 * User: demius
 * Date: 13.11.2021
 * Time: 2:57
 */
declare(strict_types=1);

namespace App\Form\Type\Project;

use App\Entity\TaskSettings;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditProjectTaskSettingsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('data_class', TaskSettings::class);
    }
}