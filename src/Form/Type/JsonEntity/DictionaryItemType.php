<?php
/**
 * User: demius
 * Date: 13.11.2021
 * Time: 17:12
 */
declare(strict_types=1);

namespace App\Form\Type\JsonEntity;

use App\Service\JsonEntity\DictionaryItem;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DictionaryItemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            'id',
            TextType::class,
            [
//                'label' => $options[''].'.label',
                'help' => 'project.task_settings.help',
            ]
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => DictionaryItem::class,

        ]);
    }
}