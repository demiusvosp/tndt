<?php
/**
 * User: demius
 * Date: 13.08.2021
 * Time: 18:52
 */
declare(strict_types=1);

namespace App\Form\Type\Project;

use App\Repository\ProjectRepository;
use App\Service\ProjectManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;
//use Tetranz\Select2EntityBundle\Form\Type\Select2EntityType;

class ProjectSelectType extends AbstractType
{
    private ProjectRepository $projectRepository;

    public function __construct(ProjectRepository $projectRepository)
    {
        $this->projectRepository = $projectRepository;
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
        $projects = $this->projectRepository->getPopularProjectsSnippets();
        foreach ($projects as $project) {
            $choices[$project->getName()] = $project->getSuffix();
        }

        return $choices;
    }
}