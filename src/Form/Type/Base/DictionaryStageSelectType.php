<?php
/**
 * User: demius
 * Date: 10.12.2021
 * Time: 0:17
 */
declare(strict_types=1);

namespace App\Form\Type\Base;

use App\Dictionary\Object\Task\StageTypesEnum;
use App\Dictionary\Object\Task\TaskStageItem;
use App\Dictionary\TypesEnum;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DictionaryStageSelectType extends DictionarySelectType
{
    public const SCENARIO_NEW = 'new';
    public const SCENARIO_EDIT = 'edit';
    public const SCENARIO_CLOSE = 'close';

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setRequired([
            'scenario'
        ]);
        $resolver->setDefault('dictionary', TypesEnum::TASK_STAGE());

        $resolver->setDefault(
            'choices',
            function(Options $options) {
                $choices = [];
                $dictionary = $this->fetcher->getDictionary(
                    TypesEnum::TASK_STAGE(),
                    $this->projectContext->getProject()
                );

                $scenario = $options['scenario'] ?? self::SCENARIO_EDIT;
                $allowedItems = array_filter(
                    $dictionary->getItems(),
                    function (TaskStageItem $item) use ($scenario) {
                        if ($scenario === self::SCENARIO_NEW
                            && $item->getType()->equals(StageTypesEnum::STAGE_ON_OPEN())
                        ) {
                            return true;
                        }
                        if ($scenario === self::SCENARIO_EDIT
                            && in_array(
                                $item->getType()->getValue(),
                                [StageTypesEnum::STAGE_ON_OPEN, StageTypesEnum::STAGE_ON_NORMAL],
                                true
                            )
                        ) {
                            return true;
                        }
                        if ($scenario === self::SCENARIO_CLOSE
                            && $item->getType()->equals(StageTypesEnum::STAGE_ON_CLOSED())
                        ) {
                            return true;
                        }
                        return false;
                    }
                );

                foreach ($allowedItems as $item) {
                    $choices[$item->getName()] = $item->getId();
                }
                return $choices;
            }
        );
    }
}