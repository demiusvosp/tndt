<?php
/**
 * User: demius
 * Date: 25.11.2021
 * Time: 20:08
 */
declare(strict_types=1);

namespace App\Form\Type\Base;

use App\Dictionary\Fetcher;
use App\Service\ProjectContext;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DictionarySelectType extends AbstractType
{
    protected Fetcher $fetcher;
    protected ProjectContext $projectContext;

    public function __construct(Fetcher $fetcher, ProjectContext $projectContext)
    {
        $this->fetcher = $fetcher;
        $this->projectContext = $projectContext;
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addModelTransformer(
            new CallbackTransformer(
                function ($value) use ($options) {
                    if (empty($value)) {
                        $dictionary = $this->fetcher->getDictionary(
                            $options['dictionary'],
                            $this->projectContext->getProject()
                        );

                        if ($dictionary->isEnabled()) {
                            return $dictionary->getDefault();
                        }
                        return 0;
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
                $items = $this->getItemsByDictionary($options['dictionary']);

                foreach ($items as $item) {
                    $choices[$item->getName()] = $item->getId();
                }
                return $choices;
            }
        );
    }

    protected function getItemsByDictionary($dictionary): array
    {
        $dictionary = $this->fetcher->getDictionary(
            $dictionary,
            $this->projectContext->getProject()
        );
        return $dictionary->getItems();
    }
}