<?php
/**
 * User: demius
 * Date: 20.02.2022
 * Time: 14:17
 */
declare(strict_types=1);

namespace App\Form\Type\Base;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MdEditType extends AbstractType
{
    public function __construct(RequestStack $requestStack)
    {
        $request = $requestStack->getMasterRequest();
        if($request) {
            $request->attributes->add(['addMdHelp' => true]);
dump($request->attributes);
        }
    }

    public const ROWS = 5;

    public function getParent(): string
    {
        return TextareaType::class;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('attr', ['rows' => self::ROWS]);
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        parent::buildView($view, $form, $options);
        $view->vars['attr']['class'] = 'md-edit';
    }
}