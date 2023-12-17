<?php
/**
 * User: demius
 * Date: 13.08.2021
 * Time: 18:52
 */
declare(strict_types=1);

namespace App\Form\Type\Project;

use App\Repository\ProjectRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProjectSelectType extends AbstractType
{
    private ProjectRepository $projectRepository;
    private Security $security;

    public function __construct(ProjectRepository $projectRepository, Security $security)
    {
        $this->projectRepository = $projectRepository;
        $this->security = $security;
    }

    public function getParent(): string
    {
//        return Select2EntityType::class;
        return ChoiceType::class;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'choices' => $this->getProjects(),
            'choice_translation_domain' => false,
        ]);
    }

    private function getProjects(): array
    {
        $choices = [];
        $projects = $this->projectRepository->getPopularProjectsSnippets(10, $this->security->getUser());
        foreach ($projects as $project) {
            $choices[$project->getName()] = $project->getSuffix();
        }

        return $choices;
    }
}