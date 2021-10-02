<?php
/**
 * User: demius
 * Date: 09.09.2021
 * Time: 21:08
 */
declare(strict_types=1);

namespace App\Form\Type\User;

use App\Repository\UserRepository;
use App\Service\ProjectManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserSelectType extends AbstractType
{
    private UserRepository $userRepository;
    private ProjectManager $projectManager;

    public function __construct(UserRepository $userRepository, ProjectManager $projectManager)
    {
        $this->userRepository = $userRepository;
        $this->projectManager = $projectManager;
    }

    public function getParent()
    {
        return ChoiceType::class;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'choice_translation_domain' => false,
            'current_project_users' => true,
        ]);
        $resolver->setDefault(
            'choices',
            function(Options $options) {
                if ($options['current_project_users']) {
                    $project = $this->projectManager->getProject();
                    return $this->getUsers($project);
                }
                return $this->getUsers(null);
            }
        );
    }

    private function getUsers($project): array
    {
        $choices = [];
        $users = $this->userRepository->getPopularUsers(10, $project ? $project->getSuffix() : null);
        foreach ($users as $user) {
            $choices[$user->getUsername()] = $user->getUsername();
        }

        return $choices;
    }
}