<?php
/**
 * User: demius
 * Date: 09.09.2021
 * Time: 21:08
 */
declare(strict_types=1);

namespace App\Form\Type\User;

use App\Repository\UserRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserSelectType extends AbstractType
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getParent()
    {
        return ChoiceType::class;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'choices' => $this->getUsers(),
            'choice_translation_domain' => false,
        ]);
    }

    private function getUsers(): array
    {
        $choices = [];
        $users = $this->userRepository->getPopularUsers();
        foreach ($users as $user) {
            $choices[$user->getUsername()] = $user->getId();
        }

        return $choices;
    }
}