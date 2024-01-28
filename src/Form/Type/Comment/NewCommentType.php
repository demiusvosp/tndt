<?php
/**
 * User: demius
 * Date: 08.11.2021
 * Time: 16:31
 */
declare(strict_types=1);

namespace App\Form\Type\Comment;

use App\Form\Type\Base\MdEditType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class NewCommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'message',
                MdEditType::class,
                [
                    'label' => false,
                    'attr' => ['rows' => 5],
                    'required' => true,
                    'constraints' => [new NotBlank(), new Length(['max' => 1000])],
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'method' => 'POST',
        ]);
    }
}