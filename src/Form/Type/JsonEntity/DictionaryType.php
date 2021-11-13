<?php
/**
 * User: demius
 * Date: 13.11.2021
 * Time: 17:10
 */
declare(strict_types=1);

namespace App\Form\Type\JsonEntity;

use App\Service\JsonEntity\Dictionary;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DictionaryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            'items',
            CollectionType::class,
            [
                'label' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'delete_empty' => true,
                'required' => false,
            ]
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'data_class' => Dictionary::class,
                'entry_type' => DictionaryItemType::class,
            ]);
    }

}