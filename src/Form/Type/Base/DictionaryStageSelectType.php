<?php
/**
 * User: demius
 * Date: 10.12.2021
 * Time: 0:17
 */
declare(strict_types=1);

namespace App\Form\Type\Base;

use App\Dictionary\TypesEnum;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DictionaryStageSelectType extends DictionarySelectType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefault('dictionary', TypesEnum::TASK_STAGE());
        $resolver->setDefault(
            'items',
            function(Options $options) {
                return $this->getItemsByDictionary(TypesEnum::TASK_STAGE());
            }
        );

        $resolver->setDefault(
            'choices',
            function(Options $options) {
                $choices = [];

                foreach ($options['items'] as $item) {
                    $choices[$item->getName()] = $item->getId();
                }
                return $choices;
            }
        );
    }
}