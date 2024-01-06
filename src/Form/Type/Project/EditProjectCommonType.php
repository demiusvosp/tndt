<?php
/**
 * User: demius
 * Date: 11.08.2021
 * Time: 23:19
 */
declare(strict_types=1);

namespace App\Form\Type\Project;

use App\Form\DTO\Project\EditProjectCommonDTO;
use App\Form\Transformer\FontAwesomeIconTransformer;
use App\Form\Type\Base\MdEditType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditProjectCommonType extends AbstractType
{
    private FontAwesomeIconTransformer $iconTransformer;

    public function __construct(FontAwesomeIconTransformer $iconTransformer)
    {
        $this->iconTransformer = $iconTransformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'name',
                TextType::class,
                ['label' => 'project.name.label', 'help' => 'project.name.help']
            )
            ->add(
                'icon',
                TextType::class,
                ['label' => 'project.icon.label', 'help' => 'project.icon.help']
            )
            ->add(
                'description',
                MdEditType::class,
                [
                    'label' => 'project.description.label',
                    'help' => 'project.description.help',
                    'required' => false,
                    'empty_data' => '',
                    'attr' => ['rows' => 10],
                ]
            );

        $builder->get('icon')->addViewTransformer($this->iconTransformer);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('data_class', EditProjectCommonDTO::class);
    }
}