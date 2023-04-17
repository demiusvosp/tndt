<?php
/**
 * User: demius
 * Date: 09.09.2021
 * Time: 21:08
 */
declare(strict_types=1);

namespace App\Form\Type\User;

use App\Repository\UserRepository;
use App\Service\ProjectContext;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserSelectType extends AbstractType
{
    private UserRepository $userRepository;
    private ProjectContext $projectContext;
    private TranslatorInterface $translator;

    public function __construct(
        UserRepository $userRepository,
        ProjectContext $projectContext,
        TranslatorInterface $translator
    ) {
        $this->userRepository = $userRepository;
        $this->projectContext = $projectContext;
        $this->translator = $translator;
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'placeholder' => false,
            'choice_translation_domain' => false,
            'current_project_users' => true,
        ]);
        $resolver->setDefault(
            'choices',
            function(Options $options) {
                $project = null;
                if ($options['current_project_users']) {
                    $project = $this->projectContext->getProject();
                }
                $users = $this->getUsers($project);

                if($options['multiple'] === true || $options['required'] === false) {
                    $users = array_merge(
                        [$this->translator->trans('nobody', ['case' => 'nominative']) => null],
                        $users
                    );
                }

                return $users;
            }
        );

        $resolver->setDefault(
            'choice_attr',
            function ($choice, $key, $value) {
                if ($choice === null) {
                    return ['class' => 'service-item'];
                }
                return [];
            }
        );
    }

    private function getUsers($project): array
    {
        $choices = [];
        $users = $this->userRepository->getPopularUsers(10, $project ? $project->getSuffix() : null);
        foreach ($users as $user) {
            $username = $user->getUsername();
            $choices[$username] = $username;
        }

        return $choices;
    }
}