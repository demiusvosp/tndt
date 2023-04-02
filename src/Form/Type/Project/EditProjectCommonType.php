<?php
/**
 * User: demius
 * Date: 11.08.2021
 * Time: 23:19
 */
declare(strict_types=1);

namespace App\Form\Type\Project;

use App\Form\DTO\Project\EditProjectCommonDTO;
use App\Form\Type\Base\MdEditType;
use App\Form\Type\User\UserSelectType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditProjectCommonType extends AbstractType
{
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
        $builder->get('icon')->addViewTransformer(
            new CallbackTransformer(
                function ($normValue) {

                    return $normValue;
                },
                function ($viewValue) {
                    return preg_replace('/<\w+ class="([\w -]+)">\w*<\/\w+>/', '${1}', $viewValue);
                }
            )
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('data_class', EditProjectCommonDTO::class);
    }
}