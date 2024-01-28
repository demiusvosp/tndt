<?php
/**
 * User: demius
 * Date: 11.08.2021
 * Time: 20:30
 */
declare(strict_types=1);

namespace App\Form\Type\Project;

use App\Form\DTO\Project\NewProjectDTO;
use App\Form\Transformer\FontAwesomeIconTransformer;
use App\Form\Type\User\UserSelectType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NewProjectType extends AbstractType
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
                'suffix',
                TextType::class,
                ['label' => 'project.suffix.label', 'help' => 'project.suffix.help']
            )
            ->add(
                'name',
                TextType::class,
                ['label' => 'project.name.label', 'help' => 'project.name.help']
            )
            ->add(
                'icon',
                TextType::class,
                ['required' => false, 'label' => 'project.icon.label', 'help' => 'project.icon.help']
            )
            ->add(
                'pm',
                UserSelectType::class,
                ['label' => 'project.pm.label', 'help' => 'project.pm.help']
            )
            ->add(
                'isPublic',
                CheckboxType::class,
                ['required' => false, 'label' => 'project.isPublic.label', 'help' => 'project.isPublic.help']
            )
            ->add(
                'description',
                TextareaType::class,
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
        $resolver->setDefault('data_class', NewProjectDTO::class);
    }
}