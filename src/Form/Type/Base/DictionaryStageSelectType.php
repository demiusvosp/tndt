<?php
/**
 * User: demius
 * Date: 10.12.2021
 * Time: 0:17
 */
declare(strict_types=1);

namespace App\Form\Type\Base;

use App\Dictionary\Object\Task\StageTypesEnum;
use App\Dictionary\Object\Task\TaskStage;
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
                /** @var TaskStage $dictionary */
                $dictionary = $this->fetcher->getDictionary(
                    TypesEnum::TASK_STAGE(),
                    $this->projectContext->getProject()
                );

                switch ($options['scenario'] ?? self::SCENARIO_EDIT) {
                    case self::SCENARIO_NEW:
                        $types = [StageTypesEnum::STAGE_ON_OPEN()];
                        break;
                    case self::SCENARIO_CLOSE:
                        $types = [StageTypesEnum::STAGE_ON_CLOSED()];
                        break;
                    case self::SCENARIO_EDIT:
                    default:
                        $types = [StageTypesEnum::STAGE_ON_OPEN, StageTypesEnum::STAGE_ON_NORMAL];
                }
                $allowedItems = $dictionary->getItemsByTypes($types);

                foreach ($allowedItems as $item) {
                    $choices[$item->getName()] = $item->getId();
                }
                return $choices;
            }
        );
    }
}