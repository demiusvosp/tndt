<?php
/**
 * User: demius
 * Date: 25.11.2021
 * Time: 20:08
 */
declare(strict_types=1);

namespace App\Form\Type\Base;

use App\Service\DictionaryService;
use App\Service\ProjectContext;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DictionarySelectType extends AbstractType
{
    private DictionaryService $dictionaryService;
    private ProjectContext $projectContext;

    public function __construct(DictionaryService $dictionaryService, ProjectContext $projectContext)
    {
        $this->dictionaryService = $dictionaryService;
        $this->projectContext = $projectContext;
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(
            new CallbackTransformer(
                function ($value) use ($options) {
                    if (empty($value)) {
                        $dictionary = $this->dictionaryService->getDictionary(
                            $options['dictionary'],
                            $this->projectContext->getProject()
                        );

                        return $dictionary->getDefaultItemId();
                    }
                    return $value;
                },
                function ($value) { return $value; }
            )
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired([
            'dictionary'
        ]);
        $resolver->setDefaults([
            'choice_translation_domain' => false,
        ]);
        $resolver->setDefault(
            'choices',
            function(Options $options) {
                $choices = [];
                $dictionary = $this->dictionaryService->getDictionary(
                    $options['dictionary'],
                    $this->projectContext->getProject()
                );

                foreach ($dictionary->getItems() as $item) {
                    $choices[$item->getName()] = $item->getId();
                }
                return $choices;
            }
        );
    }
}