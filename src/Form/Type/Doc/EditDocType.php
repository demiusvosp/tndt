<?php
/**
 * User: demius
 * Date: 29.08.2021
 * Time: 21:19
 */
declare(strict_types=1);

namespace App\Form\Type\Doc;

use App\Entity\Doc;
use App\Form\DTO\Doc\EditDocDTO;
use App\Form\Type\Base\MdEditType;
use App\Form\Type\Project\ProjectSelectType;
use App\Model\Enum\DocStateEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditDocType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'caption',
                TextType::class,
                ['label' => 'doc.caption.label', 'help' => 'doc.caption.help']
            )
            ->add(
                'state',
                ChoiceType::class,
                [
                    'label' => 'doc.state.label',
                    'help' => 'doc.state.help',
                    'choices' => [
                        'doc.state.normal.label' => DocStateEnum::Normal,
                        'doc.state.deprecated.label' => DocStateEnum::Deprecated,
                        'doc.state.archived.label' => DocStateEnum::Archived,
                    ],
                ]
            )
            ->add(
                'abstract',
                MdEditType::class,
                [
                    'label' => 'doc.abstract.label',
                    'help' => 'doc.abstract.help',
                    'attr' => ['rows' => 8],
                    'required' => false,
                    'empty_data' => '',
                ]
            )
            ->add(
                'body',
                MdEditType::class,
                [
                    'label' => 'doc.body.label',
                    'help' => 'doc.body.help',
                    'attr' => ['rows' => 40],
                    'required' => false,
                    'empty_data' => '',
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('data_class', EditDocDTO::class);
    }
}